<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncrementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('increments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('increment_type_id')->unsigned();
            $table->decimal('increment_amount',10,2);
            $table->enum('increment_amount_type',['percent','fixed']);
            $table->decimal('increment_type_amount',10,2)->nullable();
            $table->date('increment_effective_date');
            $table->text('increment_remarks')->nullable();
            $table->boolean('increment_status')->default(0);
            $table->integer('approved_by')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('increment_type_id')->references('id')->on('increment_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('increments',function(Blueprint $table){
            $table->dropForeign('increments_user_id_foreign');
            $table->dropForeign('increments_increment_type_id_foreign');
        });
        Schema::dropIfExists('increments');
    }
}
