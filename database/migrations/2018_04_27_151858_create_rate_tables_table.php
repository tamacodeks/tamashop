<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false)->unsigned();
            $table->integer('rate_group_id',false)->unsigned();
            $table->integer('cc_id',false)->unsigned();
            $table->decimal('buying_price',15,2)->default("0.00");
            $table->decimal('sale_price',15,2)->default("0.00");
            $table->decimal('sale_margin',15,2)->default("0.00");
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('rate_group_id')->references('id')->on('rate_table_groups');
            $table->foreign('cc_id')->references('id')->on('calling_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate_tables');
    }
}
