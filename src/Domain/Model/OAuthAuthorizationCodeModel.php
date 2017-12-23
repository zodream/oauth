<?php
namespace Zodream\Module\OAuth\Domain\Model;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $authorization_code
 * @property string $client_id
 * @property string $redirect_uri
 * @property string $user_id
 * @property string $expires
 * @property string $scope
 */
class OAuthAuthorizationCodeModel extends BaseModel {

    protected $primaryKey = ['authorization_code'];

    public static function tableName() {
        return 'oauth_authorization_code';
    }

    public static function findByCode($code) {
        return static::find(['authorization_code' => $code]);
    }

    /**
     * 换取access token
     * @return bool|OAuthAccessTokenModel
     */
    public function exchange() {
        if (!$this->isExpire()) {
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
}