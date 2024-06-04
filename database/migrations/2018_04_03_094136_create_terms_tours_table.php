<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms_tours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false)->unsigned();
            $table->boolean('is_page_tour')->default(0);
            $table->boolean('is_terms')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms_tours');
    }
}
