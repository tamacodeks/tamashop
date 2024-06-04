<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAledaStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aleda_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('cc_id',false)->references('id')->on('calling_cards');
            $table->integer('used_by',false)->references('id')->on('users');
            $table->string('serial',55);
            $table->string('pin',55);
            $table->string('validity',55)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aleda_statistics');
    }
}
