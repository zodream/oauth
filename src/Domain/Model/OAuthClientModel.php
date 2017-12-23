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
}