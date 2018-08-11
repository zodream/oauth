<?php
namespace Zodream\Module\OAuth\Domain\Model;

use Zodream\Helpers\Str;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $authorization_code
 * @property integer $client_id
 * @property integer $user_id
 * @property string $redirect_uri
 * @property string $expires
 * @property string $scope
 */
class OAuthAuthorizationCodeModel extends BaseModel {

    protected $primaryKey = 'authorization_code';

    public static function tableName() {
        return 'oauth_authorization_code';
    }

    protected function rules() {
        return [
            'authorization_code' => 'required|string:0,40',
            'client_id' => 'required|int',
            'user_id' => 'required|int',
            'redirect_uri' => 'string:0,200',
            'expires' => '',
            'scope' => 'string:0,200',
        ];
    }

    protected function labels() {
        return [
            'authorization_code' => 'Authorization Code',
            'client_id' => 'Client Id',
            'user_id' => 'User Id',
            'redirect_uri' => 'Redirect Uri',
            'expires' => 'Expires',
            'scope' => 'Scope',
        ];
    }

    public static function findByCode($code) {
        return static::find(['authorization_code' => $code]);
    }

    /**
     * 换取access token
     * @return bool|OAuthAccessTokenModel
     */
    public function exchange() {
        if ($this->isExpire()) {
            return false;
        }
        return OAuthAccessTokenModel::createToken($this->client_id, $this->user_id);
    }

    /**
     * 生成刷新码
     * @return OAuthRefreshTokenModel
     */
    public function createRefreshToken() {
        return OAuthRefreshTokenModel::createToken($this->client_id, $this->user_id);
    }

    public function generateCode() {
        return md5(sprintf('%s%s%s%s', $this->client_id, $this->user_id, $this->scope, Str::random()));
    }
}