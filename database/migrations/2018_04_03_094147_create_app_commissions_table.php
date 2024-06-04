<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id',false)->unsigned();
            $table->decimal('prev_com',15,2)->default("0.00");
            $table->decimal('commission',15,2)->default("0.00");
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_commissions');
    }
}
