<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Module\OAuth\Domain\Model\OAuthAuthorizationCodeModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;
use Zodream\Service\Http\Request;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class TokenController extends Controller {

    public function indexAction(Request $request) {
        $grant_type = $request->get('grant_type');
        if ($grant_type !== 'authorization_code') {
            return $this->getToken($request);
        }
        if ($grant_type !== 'refresh_token') {
            return $this->refreshToken($request);
        }
        return $this->renderFailure('unknown grant_type');
    }

    public function getToken(Request $request) {
        $data = $request->get('grant_type,code,redirect_uri,client_id');
        $codeModel = OAuthAuthorizationCodeModel::findByCode($data['code']);
        $tokenModel = $codeModel->exchange();
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->renderFailure('code is expired!', 401);
        }
        $refreshTokenModel = $codeModel->createRefreshToken();
        return $this->render([
            'user_id' => $codeModel->user_id,
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }

    public function refreshToken(Request $request) {
        $data = $request->get('grant_type,refresh_token,scope');
        $refreshTokenModel = OAuthRefreshTokenModel::findByToken($data['refresh_token']);
        if (empty($refreshTokenModel)) {
            return $this->renderFailure('error refresh_token', 401);
        }
        $tokenModel = $refreshTokenModel->refreshToken();
        return $this->render([
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }
}