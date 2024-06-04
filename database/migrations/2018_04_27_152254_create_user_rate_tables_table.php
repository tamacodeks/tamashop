<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_rate_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false)->unsigned();
            $table->integer('rate_group_id',false)->unsigned();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rate_group_id')->references('id')->on('rate_table_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_rate_tables');
    }
}
