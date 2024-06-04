<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackSalesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date')->nullable();
            $table->integer('service_id',false)->unsigned();
            $table->integer('order_id',false)->unsigned();
            $table->text('product_name')->nullable();
            $table->integer('operator_id',false)->nullable();
            $table->string('operator',255)->nullable();
            $table->string('mobile',25)->nullable();
            $table->integer('count',false)->default(1);
            $table->decimal('amount',15,2)->nullable();
            $table->integer('created_by',false)->nullable();

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('track_sales');
    }
}
