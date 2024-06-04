<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('TransID',15)->unique();
            $table->integer('order_id',false)->unsigned();
            $table->string('status',255)->nullable();
            $table->boolean('error_code')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();

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
        Schema::dropIfExists('track_status');
    }
}
