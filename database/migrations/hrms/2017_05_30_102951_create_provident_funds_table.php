<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidentFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provident_funds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->decimal('pf_percent_amount',5,2);
            $table->date('pf_effective_date');
            $table->enum('pf_interest_calculate',['monthly','yearly']);
            $table->boolean('pf_status')->default(1);
            $table->integer('approved_by')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
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
        Schema::table('provident_funds',function(Blueprint $table){
            $table->dropForeign('provident_funds_user_id_foreign');
        });
        Schema::dropIfExists('provident_funds');
    }
}
