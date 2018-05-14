<?php
namespace Zodream\Module\OAuth\Domain;


use Zodream\Http\Http;
use Zodream\ThirdParty\OAuth\BaseOAuth;

class Client extends BaseOAuth {

    const BASE_HOST = 'http://zodream.cn/oauth/';

    protected function getUri($path) {
        return self::BASE_HOST.$path;
    }

    /**
     * @return Http
     */
    public function getLogin() {
        return $this->getBaseHttp()
            ->url($this->getUri('authorize'), [
                'response_type' => 'code',
                '#client_id',
                '#redirect_uri',
                'state',
                'scope',
            ]);
    }

    public function getAccess() {
        return $this->getBaseHttp()
            ->url($this->getUri('token'), [
                'grant_type' => 'authorization_code',
                '#client_id',
                '#client_secret',
                '#code',
                '#redirect_uri'
            ]);
    }

    public function getRefresh() {
        return $this->getBaseHttp()
            ->url($this->getUri('token'),
                [
                    'grant_type' => 'refresh_token',
                    '#client_id',
                    '#client_secret',
                    '#refresh_token'
                ]);
    }

    public function getInfo() {
        return $this->getBaseHttp()
            ->url($this->getUri('user'), [
                '#client_id',
                '#access_token'
            ]);
    }

    public function callback() {
        if (parent::callback() === false) {
            return false;
        }
        /**
         * access_token	授权令牌，Access_Token。
         * expires_in	该access token的有效期，单位为秒。
         * refresh_token
         */
        $access = $this->getAccess()->json();
        if (!is_array($access) || !array_key_exists('access_token', $access)) {
            return false;
        }
        $access['identity'] = $access['user_id'];
        $this->set($access);
        return $access;
    }

    /**
     * 获取用户信息
     * @return array
     */
    public function info() {
        $user = $this->getInfo()->json();
        if (!is_array($user) || !array_key_exists('username', $user)) {
            return false;
        }
        $this->set($user);
        return $user;
    }
}