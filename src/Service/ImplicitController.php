<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Helpers\Str;
use Zodream\Http\Uri;
use Zodream\Infrastructure\Http\Request;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/26
 * Time: 15:02
 */
class ImplicitController extends Controller {

    public function indexAction(
        $response_type,
        $client_id,
        $redirect_uri,
        $scope = null,
        $state = null) {
        $uri = new Uri($redirect_uri);
        if ($response_type != 'token') {
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
        $tokenModel = OAuthAccessTokenModel::createToken($model->id, 0);
        if (empty($tokenModel) || empty($tokenModel->access_token)) {
            return $this->redirect($uri->addData([
                'error' => 'error token',
                'state' => $state
            ]));
        }
        return $this->redirect($uri->addData([
            'access_token' => $tokenModel->access_token,
            'token_type' => '',
            'expires_in' => 3600,
            'state' => $state
        ]));
    }
}