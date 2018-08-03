<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Database\Model\UserModel;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Service\Config;


class UserController extends Controller {

    public function indexAction() {
        $data = app('request')->get('access_token,client_id');
        $model = OAuthAccessTokenModel::findByToken($data['access_token']);
        if (empty($model)) {
            return $this->jsonFailure('token is error', 401);
        }
        $userClass = Config::auth('model');
        if (empty($userClass)) {
            return $this->jsonFailure('user is error!', 401);
        }
        /** @var UserModel $user */
        $user = call_user_func($userClass.'::findByIdentity', $model->user_id);
        if (empty($user)) {
            return $this->jsonFailure('user is error!', 401);
        }
        return $this->json([
            'user_id' => $user->getIdentity(),
            'username' => $user->name,
            'avatar' => url()->asset($user->avatar),
            'sex' => $user->sex,
        ]);
    }

}