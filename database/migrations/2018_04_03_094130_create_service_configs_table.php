<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id',false)->unsigned();
            $table->integer('country_id',false)->unsigned()->nullable();
            $table->text('config')->nullable();
            $table->string('type',155)->nullable();
            $table->text('tel_prefix')->nullable();
            $table->string('count_prefix',5)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_configs');
    }
}
