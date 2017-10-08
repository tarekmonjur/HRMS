<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationToProvidentFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provident_funds',function(Blueprint $table){
            if(Schema::hasTable('provident_funds')){
                $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::table('provident_funds',function(Blueprint $table){
            $table->dropForeign('provident_funds_user_id_foreign');
        });
    }
}
