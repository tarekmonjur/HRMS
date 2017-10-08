<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('menu_parent_id')->default(0);
            $table->integer('module_id')->unsigned();
            $table->string('menu_name')->length(50)->nullable();
            $table->string('menu_url')->length(50);
            $table->string('menu_section_name')->length(50)->nullable();
            $table->string('menu_icon_class')->length(50)->nullable();
            $table->boolean('menu_status')->default(1)->comment='1=active, 0=inactive';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('menus', function(Blueprint $table){
        //     $table->dropForeign('menus_module_id_foreign');
        // });

        Schema::dropIfExists('menus');
    }
}
