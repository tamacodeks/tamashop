<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('pin_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cc_id',false)->unsigned();
            $table->dateTime('date');
            $table->string("name",255);
            $table->string("pin",55);
            $table->string("serial",55);
            $table->string('validity')->nullable();
            $table->boolean('is_aleda')->default(0);
            $table->integer('used_by',false)->unsigned();

            $table->foreign('cc_id')->references('id')->on('calling_cards');
            $table->foreign('used_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_histories');
    }
}
