<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallingCardPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calling_card_pins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cc_id',false)->unsigned();
            $table->string('name',255);
            $table->string('value',10);
            $table->string('pin',255);
            $table->string('serial',25);
            $table->boolean('is_used')->default(0);
            $table->integer('used_by')->unsigned()->nullable();
            $table->boolean('is_blocked')->default(0);
            $table->boolean('is_locked')->default(0);
            $table->integer('locked_by')->unsigned()->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->text('usage_note')->nullale();
            $table->string('up_trans_id',55)->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->string('public_key',100)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('cc_id')->references('id')->on('calling_cards');
            $table->foreign('used_by')->references('id')->on('users');
            $table->foreign('locked_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calling_card_pins');
    }
}
