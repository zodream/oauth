<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Database\Model\UserModel;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Service\Http\Request;


class UserController extends Controller {

    public function indexAction(Request $request) {
        $data = $request->get('access_token,client_id');
        $model = OAuthAccessTokenModel::findByToken($data['access_token']);
        if (empty($model)) {
            return $this->renderFailure('token is error', 401);
        }
        $userClass = config('auth.model');
        if (empty($userClass)) {
            return $this->renderFailure('user is error!', 401);
        }
        /** @var UserModel $user */
        $user = call_user_func($userClass.'::findByIdentity', $model->user_id);
        if (empty($user)) {
            return $this->renderFailure('user is error!', 401);
        }
        return $this->render([
            'user_id' => $user->getIdentity(),
            'username' => $user->name,
            'avatar' => url()->asset($user->avatar),
            'sex' => $user->sex,
        ]);
    }

}