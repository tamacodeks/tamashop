<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinPrintRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_print_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user',false)->unsigned()->nullable();
            $table->integer('to_user',false)->unsigned()->nullable();
            $table->integer('pin_id',false)->unsigned()->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->boolean('status')->default(0)->nullable();

            $table->foreign('from_user')->references('id')->on('users');
            $table->foreign('to_user')->references('id')->on('users');
            $table->foreign('pin_id')->references('id')->on('calling_card_pins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_print_requests');
    }
}
