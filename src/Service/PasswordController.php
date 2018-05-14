<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Database\Model\UserModel;
use Zodream\Infrastructure\Http\Request;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;
use Zodream\Service\Config;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class PasswordController extends Controller {

    public function indexAction() {
        list($clientId, $clientSecret) = $this->getBasicAuthCredentials();
        $client = OAuthClientModel::findByClient($clientId, $clientSecret);
        if (empty($client)) {
            return $this->jsonFailure('client_id is error!', 401);
        }
        $data = Request::request('grant_type,username,password,scope');
        if ($data['grant_type'] !== 'password') {
            return $this->jsonFailure('grant_type is error!', 401);
        }
        $userClass = Config::auth('model');
        if (empty($userClass)) {
            return $this->jsonFailure('grant_type is error!', 401);
        }
        /** @var UserModel $user */
        $user = call_user_func($userClass.'::findByAccount', $data['username'], $data['password']);
        if (empty($user)) {
            return $this->jsonFailure('username is error!', 401);
        }
        $tokenModel = OAuthAccessTokenModel::createToken($client->id, $user->getIdentity());
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->jsonFailure('token is error!', 401);
        }
        $refreshTokenModel = OAuthRefreshTokenModel::createToken($client->id, $user->getIdentity());
        return $this->json([
            'user_id' => $user->getIdentity(),
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }
}