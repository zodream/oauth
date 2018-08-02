<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Domain\Access\Auth;
use Zodream\Helpers\Str;
use Zodream\Helpers\Time;
use Zodream\Http\Uri;
use Zodream\Infrastructure\Http\Request;
use Zodream\Infrastructure\Http\Response;
use Zodream\Module\OAuth\Domain\Model\OAuthAuthorizationCodeModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientUserModel;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class AuthorizeController extends Controller {

    public function indexAction(
        $response_type,
        $client_id,
        $redirect_uri,
        $scope = null,
        $state = null) {
        $uri = new Uri($redirect_uri);
        if ($response_type != 'code') {
            return $this->redirect($uri->addData([
                'error' => 'error response_type',
                'state' => $state
            ]));
        }

        $model = OAuthClientModel::findByClientId($client_id);
        if (empty($model)) {
            return $this->redirect($uri->addData([
                'error' => 'error client_id',
                'state' => $state
            ]));
        }
        if (!$model->isValidUri($redirect_uri)) {
            return $this->redirect($uri->addData([
                'error' => 'error redirect_uri',
                'state' => $state
            ]));
        }
        if (auth()->guest()) {
            return $this->redirectWithAuth();
        }
        $history = OAuthClientUserModel::where([
            'client_id' => $model->id,
            'user_id' => auth()->id()
        ])->count();
        if ($history > 0) {
            return $this->redirectWithCode($model->id, $uri, $scope, $state);
        }
        if (!app('request')->isPost()) {
            return $this->show();
        }
        return $this->redirectWithCode($model->id, $uri, $scope, $state);
    }

    /**
     * 生成新的并返回
     * @param $client_id
     * @param Uri $redirect_uri
     * @param null $scope
     * @param null $state
     * @return Response
     * @throws \Exception
     */
    protected function redirectWithCode($client_id,
                                        Uri $redirect_uri,
                                        $scope = null,
                                        $state = null) {
        $model = new OAuthAuthorizationCodeModel();
        $model->client_id = $client_id;
        $model->redirect_uri = (string)$redirect_uri;
        $model->user_id = auth()->id();
        $model->scope = $scope;
        $model->authorization_code = $model->generateCode();
        $model->expires = Time::timestamp(time() + 3600);
        $model->save();
        OAuthClientUserModel::create([
            'client_id' => $client_id,
            'user_id' => auth()->id()
        ]);
        return $this->redirect($redirect_uri->addData([
            'code' => $model->authorization_code,
            'state' => $state
        ]));
    }




}