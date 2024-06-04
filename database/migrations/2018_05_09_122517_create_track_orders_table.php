<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trans_id',15)->unique()->nullable();
            $table->integer('user_id',false)->unsigned()->nullable();
            $table->integer('order_id',false)->unsigned()->nullable();
            $table->integer('order_status_id',false)->unsigned()->nullable();
            $table->boolean('status')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('used_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('order_status_id')->references('id')->on('order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('track_orders');
    }
}
