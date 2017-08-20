<?php
namespace Zodream\Module\OAuth\Domain\Migrations;

use Zodream\Database\Migrations\Migration;
use Zodream\Database\Schema\Schema;
use Zodream\Database\Schema\Table;
use Zodream\Module\OAuth\Domain\OAuthAccessTokenModel;
use Zodream\Module\OAuth\Domain\OAuthAuthorizationCodeModel;
use Zodream\Module\OAuth\Domain\OAuthClientModel;
use Zodream\Module\OAuth\Domain\OAuthClientUserModel;
use Zodream\Module\OAuth\Domain\OAuthRefreshTokenModel;

class CreateOAuthTables extends Migration {
    public function up() {
        Schema::createTable(OAuthAccessTokenModel::tableName(), function(Table $table) {
            $table->set('access_token')->varchar(40)->pk();
            $table->set('client_id')->notNull()->varchar(80);
            $table->set('user_id')->varchar(255);
            $table->set('expires')->notNull()->timestamp();
            $table->set('scope')->varchar(200);
        });
        Schema::createTable(OAuthAuthorizationCodeModel::tableName(), function(Table $table) {
            $table->set('authorization_code')->varchar(40)->pk();
            $table->set('client_id')->notNull()->varchar(80);
            $table->set('user_id')->varchar(255);
            $table->set('redirect_uri')->varchar(200);
            $table->set('expires')->notNull()->timestamp();
            $table->set('scope')->varchar(200);
        });
        Schema::createTable(OAuthClientModel::tableName(), function(Table $table) {
            $table->set('id')->pk();
            $table->set('client_id')->varchar(80)->unique();
            $table->set('client_secret')->notNull()->varchar(80);
            $table->set('redirect_uri')->notNull()->varchar(200);
            $table->set('user_id')->notNull(10)->int();
            $table->set('update_at')->int(10);
            $table->set('create_at')->int(10);
        });
        Schema::createTable(OAuthClientUserModel::tableName(), function(Table $table) {
            $table->set('id')->int()->pk();
            $table->set('client_id')->notNull(10)->int();
            $table->set('user_id')->notNull(10)->int();
            $table->set('create_at')->int(10);
        });
        Schema::createTable(OAuthRefreshTokenModel::tableName(), function(Table $table) {
            $table->set('refresh_token')->varchar(40)->pk();
            $table->set('client_id')->notNull()->varchar(80);
            $table->set('user_id')->varchar(255);
            $table->set('expires')->notNull()->timestamp();
            $table->set('scope')->varchar(200);
        });
    }

    public function down() {
        Schema::dropTable(OAuthAccessTokenModel::tableName());
        Schema::dropTable(OAuthAuthorizationCodeModel::tableName());
        Schema::dropTable(OAuthClientModel::tableName());
        Schema::dropTable(OAuthClientUserModel::tableName());
        Schema::dropTable(OAuthRefreshTokenModel::tableName());
    }
}