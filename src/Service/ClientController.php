<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Http\Uri;
use Zodream\Infrastructure\Http\Request;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class ClientController extends Controller {

    public function indexAction() {
        $data = app('request')->get('grant_type,scope');
        if ($data['grant_type'] !== 'client_credentials') {
            return $this->jsonFailure('grant_type error');
        }
        list($clientId, $clientSecret) = $this->getBasicAuthCredentials();
        $client = OAuthClientModel::findByClient($clientId, $clientSecret);
        if (empty($client)) {
            return $this->jsonFailure('client_id is error!', 401);
        }
        $tokenModel = OAuthAccessTokenModel::createToken($client->id, 0);
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->jsonFailure('token is expired!', 401);
        }
        return $this->json([
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600
            //'scope'
        ]);
    }
}