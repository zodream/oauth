<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Database\Model\UserModel;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;
use Zodream\Service\Http\Request;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class PasswordController extends Controller {

    public function indexAction(Request $request) {
        list($clientId, $clientSecret) = $this->getBasicAuthCredentials();
        $client = OAuthClientModel::findByClient($clientId, $clientSecret);
        if (empty($client)) {
            return $this->renderFailure('client_id is error!', 401);
        }
        $data = $request->get('grant_type,username,password,scope');
        if ($data['grant_type'] !== 'password') {
            return $this->renderFailure('grant_type is error!', 401);
        }
        $userClass = config('auth.model');
        if (empty($userClass)) {
            return $this->renderFailure('grant_type is error!', 401);
        }
        /** @var UserModel $user */
        $user = call_user_func($userClass.'::findByAccount', $data['username'], $data['password']);
        if (empty($user)) {
            return $this->renderFailure('username is error!', 401);
        }
        $tokenModel = OAuthAccessTokenModel::createToken($client->id, $user->getIdentity());
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->renderFailure('token is error!', 401);
        }
        $refreshTokenModel = OAuthRefreshTokenModel::createToken($client->id, $user->getIdentity());
        return $this->render([
            'user_id' => $user->getIdentity(),
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'refresh_token' => $refreshTokenModel->refresh_token,
            //'scope'
        ]);
    }
}