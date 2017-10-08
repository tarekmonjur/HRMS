<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSetupPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_name')->length(150);
            $table->float('package_price')->length( 9, 2);
            $table->integer('package_duration')->length(6);
            $table->tinyInteger('package_type')->default(2)->comment='1=free, 2=paid';
            $table->tinyInteger('package_level_limit')->nullable();
            $table->integer('package_user_limit')->nullable();
            $table->tinyInteger('package_sister_concern_limit')->nullable();
            $table->boolean('package_status')->default(1)->comment='1=active, 0=inactive';
            $table->integer('package_created_by');
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
        Schema::dropIfExists('packages');
    }
}
