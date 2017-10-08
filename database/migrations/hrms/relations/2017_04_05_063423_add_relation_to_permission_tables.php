<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationToPermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus',function(Blueprint $table){
            if(Schema::hasTable('menus')){
                $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            }
        });

        Schema::table('user_permissions',function(Blueprint $table){
            if(Schema::hasTable('user_permissions')){
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            }
        });

        Schema::table('level_permissions',function(Blueprint $table){
            if(Schema::hasTable('level_permissions')){
                $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign('menus_module_id_foreign');
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign('user_permissions_user_id_foreign');
            $table->dropForeign('user_permissions_menu_id_foreign');
        });
        Schema::table('level_permissions', function (Blueprint $table) {
            $table->dropForeign('level_permissions_level_id_foreign');
            $table->dropForeign('level_permissions_menu_id_foreign');
        });
    }
}
