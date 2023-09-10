<?php
declare(strict_types=1);
namespace Zodream\Module\OAuth\Domain\Migrations;

use Zodream\Database\Migrations\Migration;
use Zodream\Database\Schema\Table;
use Zodream\Module\OAuth\Domain\Model\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\Model\OAuthAuthorizationCodeModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientModel;
use Zodream\Module\OAuth\Domain\Model\OAuthClientUserModel;
use Zodream\Module\OAuth\Domain\Model\OAuthRefreshTokenModel;

class CreateOAuthTables extends Migration {
    public function up(): void {
        $this->append(OAuthAccessTokenModel::tableName(), function(Table $table) {
            $table->string('access_token', 40)->pk();
            $table->int('client_id');
            $table->int('user_id');
            $table->timestamp('expires');
            $table->string('scope', 200)->default('');
        })->append(OAuthAuthorizationCodeModel::tableName(), function(Table $table) {
            $table->string('authorization_code', 40)->pk();
            $table->int('client_id');
            $table->int('user_id');
            $table->string('redirect_uri', 200);
            $table->timestamp('expires');
            $table->string('scope', 200);
        })->append(OAuthClientModel::tableName(), function(Table $table) {
            $table->int('id')->pk(true);
            $table->string('client_id', 80)->unique();
            $table->string('client_secret', 80);
            $table->string('redirect_uri', 200);
            $table->int('user_id');
            $table->timestamps();
        })->append(OAuthClientUserModel::tableName(), function(Table $table) {
            $table->int('id')->pk(true);
            $table->int('client_id');
            $table->int('user_id');
            $table->timestamp('created_at');
        })->append(OAuthRefreshTokenModel::tableName(), function(Table $table) {
            $table->string('refresh_token', 40)->pk();
            $table->int('client_id');
            $table->int('user_id');
            $table->timestamp('expires');
            $table->string('scope', 200);
        })->autoUp();
    }
}