<?php
namespace Zodream\Module\OAuth;
/**
 * Created by PhpStorm.
 * User: ZoDream
 * Date: 2017/1/1
 * Time: 19:22
 */
use Zodream\Module\OAuth\Domain\Migrations\CreateOAuthTables;
use Zodream\Route\Controller\Module as BaseModule;

class Module extends BaseModule {
    public function getMigration() {
        return new CreateOAuthTables();
    }
}