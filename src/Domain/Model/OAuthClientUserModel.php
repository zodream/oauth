<?php
namespace Zodream\Module\OAuth\Domain\Model;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 * @property integer $id
 * @property integer $client_id
 * @property integer $user_id
 * @property integer $create_at
 */
class OAuthClientUserModel extends BaseModel {
    public static function tableName(): string {
        return 'oauth_client_user';
    }

    protected function rules(): array {
        return [
            'client_id' => 'required|int',
            'user_id' => 'required|int',
            'created_at' => 'int',
        ];
    }

    protected function labels(): array {
        return [
            'id' => 'Id',
            'client_id' => 'Client Id',
            'user_id' => 'User Id',
            'created_at' => 'Created At',
        ];
    }
}