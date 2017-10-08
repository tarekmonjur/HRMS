<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('bonus_type_id')->unsigned();
            $table->enum('bonus_amount_type',['percent','fixed']);
            $table->decimal('bonus_type_amount',5,2)->nullable();
            $table->decimal('bonus_amount',10,2);
            $table->date('bonus_effective_date');
            $table->text('bonus_remarks')->nullable();
            $table->integer('approved_by')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('bonus_type_id')->references('id')->on('bonus_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonuses',function(Blueprint $table){
            $table->dropForeign('bonuses_user_id_foreign');
            $table->dropForeign('bonuses_bonus_type_id_foreign');
        });
        Schema::dropIfExists('bonuses');
    }
}
