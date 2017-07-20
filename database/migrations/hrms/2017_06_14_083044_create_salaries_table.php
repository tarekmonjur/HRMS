<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->decimal('basic_salary',10,2)->default(0.00);
            $table->decimal('salary_in_cash',10,2)->default(0.00);
            $table->string('salary_month',10);
            $table->tinyInteger('salary_days')->default(0);
            $table->enum('salary_pay_type', ['partial','full'])->nullable();
            $table->integer('work_hour')->default(0);
            $table->integer('overtime_hour')->default(0);
            $table->decimal('overtime_amount',8,2)->default(0.00);
            $table->text('attendance_info')->nullable();
            $table->text('allowance_info')->nullable();
            $table->text('deduction_info')->nullable();
            $table->decimal('total_allowance',14,2)->default(0.00);
            $table->decimal('total_deduction',14,2)->default(0.00);
            $table->decimal('perhour_salary',10,2)->default(0.00);
            $table->decimal('perday_salary',10,2)->default(0.00);
            $table->decimal('salary',14,2)->default(0.00);
            $table->decimal('gross_salary',14,2)->default(0.00);
            $table->decimal('net_salary',14,2)->default(0.00);
            $table->decimal('total_salary',16,2)->default(0.00);
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('salaries');
    }
}
