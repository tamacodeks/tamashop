<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelecomProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telecom_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tp_config_id',false)->unsigned();
            $table->string('name',255);
            $table->text('description')->nullable();
            $table->decimal('face_value',15,2)->default("0.00");
            $table->boolean('status')->default(1);
            $table->integer('ordering',false)->nullable();

            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('tp_config_id')->references('id')->on('telecom_providers_config');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telecom_providers');
    }
}
