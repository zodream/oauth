<?php
namespace Zodream\Module\OAuth\Domain;

use Zodream\Helpers\Time;


/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $refresh_token
 * @property string $client_id
 * @property string $user_id
 * @property string $expires
 * @property string $scope
 */
class OAuthRefreshTokenModel extends BaseModel {

    protected $primaryKey = ['refresh_token'];

    public static function tableName() {
        return 'oauth_refresh_token';
    }

    /**
     * @param string $token
     * @return static
     */
    public static function findByToken($token) {
        return static::where(['refresh_token' => $token,
            'expires' => ['<=', Time::timestamp()]])
            ->one();
    }

    /**
     * 刷新并生成 access token
     * @return bool|OAuthAccessTokenModel
     */
    public function refreshToken() {
        if (!$this->delete()) {
            return false;
        }
        $this->isNewRecord = true;
        $this->refresh_token = $this->generateAccessToken();
        $this->expires = Time::timestamp(time() + 3600 * 24 * 365);
        $this->save();
        return OAuthAccessTokenModel::createToken($this->client_id, $this->user_id);
    }

    public static function createToken($client_id, $user_id) {
        static::where(['client_id' => $client_id, 'user_id' => $user_id])
            ->delete();
        return static::create([
            'refresh_token' => static::generateAccessToken(),
            'user_id' => $user_id,
            'client_id' => $client_id,
            'expires' => Time::timestamp(time() + 3600 * 24 * 365)
        ]);
    }
}