<?php
namespace Zodream\Module\OAuth\Domain;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property integer $id
 * @property integer $client_id
 * @property integer $user_id
 * @property integer $create_at
 */
class OAuthClientUserModel extends BaseModel {
    public static function tableName() {
        return 'oauth_client_user';
    }
}