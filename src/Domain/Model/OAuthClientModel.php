<?php
namespace Zodream\Module\OAuth\Domain\Model;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $id
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 * @property integer $user_id
 * @property integer $update_at
 * @property integer $create_at
 */
class OAuthClientModel extends BaseModel {
    public static function tableName() {
        return 'oauth_client';
    }

    protected function rules() {
        return [
            'client_id' => 'string:0,80',
            'client_secret' => 'required|string:0,80',
            'redirect_uri' => 'required|string:0,200',
            'user_id' => 'required|int',
            'created_at' => 'int',
            'updated_at' => 'int',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'client_id' => 'Client Id',
            'client_secret' => 'Client Secret',
            'redirect_uri' => 'Redirect Uri',
            'user_id' => 'User Id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 判断是否是有效的回调网址
     * @param $uri
     * @return bool
     */
    public function isValidUri($uri) {
        return parse_url($uri, PHP_URL_HOST) === $this->redirect_uri;
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @return OAuthClientModel
     */
    public static function findByClient($clientId, $clientSecret) {
        return self::where('client_id', $clientId)->where('client_secret', $clientSecret)->one();
    }

    /**
     * @param $clientId
     * @return OAuthClientModel
     */
    public static function findByClientId($clientId) {
        return self::where('client_id', $clientId)->one();
    }
}