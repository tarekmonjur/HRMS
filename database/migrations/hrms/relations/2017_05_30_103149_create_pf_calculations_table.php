<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePfCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pf_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provident_fund_id')->unsigned();
            $table->decimal('pf_percent',5,2);
            $table->decimal('pf_amount',16,3);
            $table->decimal('pf_interest_percent',5,2);
            $table->decimal('pf_interest_amount',12,2);
            $table->decimal('pf_debit',12,2)->default(0)->comment="(-) subtract to provident fund";
            $table->decimal('pf_credit',12,2)->default(0)->comment="(+) added to provident fund";
            $table->string('pf_date',10);
            $table->tinyInteger('pf_observation')->default(0)->comment="0=other ,1=salary deduct";
            $table->text('pf_remarks');
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('provident_fund_id')->references('id')->on('provident_funds')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pf_calculations',function(Blueprint $table){
            $table->dropForeign('pf_calculations_provident_fund_id_foreign');
        });
        Schema::dropIfExists('pf_calculations');
    }
}
