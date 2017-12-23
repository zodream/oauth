<?php
namespace Zodream\Module\OAuth\Domain\Model;

use Zodream\Helpers\Time;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $access_token
 * @property string $client_id
 * @property string $user_id
 * @property string $expires
 * @property string $scope
 */
class OAuthAccessTokenModel extends BaseModel {
    protected $primaryKey = ['access_token'];

    public static function tableName() {
        return 'oauth_access_token';
    }

    public function refreshToken() {
        if (!$this->delete()) {
            return false;
        }
        $this->isNewRecord = true;
        $this->access_token = $this->generateAccessToken();
        $this->expires = Time::timestamp(time() + 3600);
        return $this->save();
    }

    /**
     * @param $client_id
     * @param $user_id
     * @return static
     */
    public static function createToken($client_id, $user_id) {
        static::where(['client_id' => $client_id, 'user_id' => $user_id])
            ->delete();
        return static::create([
            'access_token' => static::generateAccessToken(),
            'user_id' => $user_id,
            'client_id' => $client_id,
            'expires' => Time::timestamp(time() + 3600)
        ]);
    }
}