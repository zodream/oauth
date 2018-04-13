<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Infrastructure\Http\Request;
use Zodream\Module\OAuth\Domain\Model\OAuthAuthorizationCodeModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class TokenController extends Controller {

    public function indexAction() {
        $grant_type = Request::post('grant_type');
        if ($grant_type !== 'authorization_code') {
            return $this->getToken();
        }
        if ($grant_type !== 'refresh_token') {
            return $this->refreshToken();
        }
        return $this->jsonFailure('unknown grant_type');
    }

    public function getToken() {
        $data = Request::request('grant_type,code,redirect_uri,client_id');
        $codeModel = OAuthAuthorizationCodeModel::findByCode($data['code']);
        $tokenModel = $codeModel->exchange();
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->jsonFailure('code is expired!', 401);
        }
        $refreshTokenModel = $codeModel->createRefreshToken();
        return $this->json([
            'user_id' => $codeModel->user_id,
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }

    public function refreshToken() {
        $data = Request::request('grant_type,refresh_token,scope');
        $refreshTokenModel = OAuthRefreshTokenModel::findByToken($data['refresh_token']);
        if (empty($refreshTokenModel)) {
            return $this->jsonFailure('error refresh_token', 401);
        }
        $tokenModel = $refreshTokenModel->refreshToken();
        return $this->json([
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }
}