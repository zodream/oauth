<?php
namespace Zodream\Module\OAuth\Domain\Model;

use Zodream\Database\Model\Model;
use Zodream\Helpers\Str;

/**
 * Class OauthClientModel
 * @package Zodream\Module\OAuth\Domain
 *
 */
abstract class BaseModel extends Model {

    /**
     * 生成 access token
     * @return string
     */
    public static function generateAccessToken() {
        return bin2hex(Str::randomBytes(20));
    }

    /**
     * 是否过期
     * @return bool
     */
    public function isExpire() {
        return strtotime($this->expires) < time();
    }
}