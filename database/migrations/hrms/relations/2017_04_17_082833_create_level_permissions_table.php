<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('level_id')->unsigned();
            $table->integer('menu_id')->unsigned();
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('restrict');
            // $table->foreign('menu_id')->references('id')->on('menus')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('level_permissions',function (Blueprint $table){
            $table->dropForeign('level_permissions_level_id_foreign');
            // $table->dropForeign('level_permissions_menu_id_foreign');
        });

        Schema::dropIfExists('level_permissions');
    }
}
