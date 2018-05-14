<?php
namespace Zodream\Module\OAuth\Domain\Model;

use Zodream\Helpers\Time;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $access_token
 * @property integer $client_id
 * @property integer $user_id
 * @property string $expires
 * @property string $scope
 */
class OAuthAccessTokenModel extends BaseModel {
    protected $primaryKey = ['access_token'];

    public static function tableName() {
        return 'oauth_access_token';
    }

    protected function rules() {
        return [
            'access_token' => 'required|string:0,40',
            'client_id' => 'required|int',
            'user_id' => 'required|int',
            'expires' => '',
            'scope' => 'string:0,200',
        ];
    }

    protected function labels() {
        return [
            'access_token' => 'Access Token',
            'client_id' => 'Client Id',
            'user_id' => 'User Id',
            'expires' => 'Expires',
            'scope' => 'Scope',
        ];
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

    /**
     * @param $token
     * @return static
     */
    public static function findByToken($token) {
        return static::where(['access_token' => $token,
            'expires' => ['<=', Time::timestamp()]])
            ->one();
    }
}