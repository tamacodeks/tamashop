<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallingCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calling_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('telecom_provider_id',false)->unsigned();
            $table->integer('service_id',false)->unsigned();
            $table->string('name',255);
            $table->text('description')->nullable();
            $table->text('validity')->nullable();
            $table->text('access_number')->nullable();
            $table->decimal('buying_price',15,2)->default("0.00");
            $table->decimal('face_value',15,2)->default("0.00");
            $table->text('comment_1')->nullable();
            $table->text('comment_2')->nullable();
            $table->boolean('status')->default(1);
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('telecom_provider_id')->references('id')->on('telecom_providers');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calling_cards');
    }
}
