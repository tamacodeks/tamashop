<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id',false)->unsigned();
            $table->integer('product_id',false)->unsigned()->nullable();
            $table->string('sender_first_name',55)->nullable();
            $table->string('sender_last_name',55)->nullable();
            $table->string('sender_mobile',15)->nullable();
            $table->string('sender_email',85)->nullable();
            $table->text('sender_address')->nullable();
            $table->string('receiver_first_name',55)->nullable();
            $table->string('receiver_last_name',55)->nullable();
            $table->string('receiver_mobile',15)->nullable();
            $table->string('receiver_email',85)->nullable();
            $table->text('receiver_address')->nullable();
            $table->string('tama_pin',15)->nullable();
            $table->string('tama_serial',15)->nullable();
            $table->string('tt_mobile',15)->nullable();
            $table->decimal('tt_euro_amount',15,2)->nullable();
            $table->decimal('tt_dest_amount',15,2)->nullable();
            $table->string('tt_dest_currency',15)->nullable();
            $table->string('tt_operator',155)->nullable();
            $table->string('app_mobile',15)->nullable();
            $table->decimal('app_old_balance',15,2)->nullable();
            $table->decimal('app_new_balance',15,2)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

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
        Schema::dropIfExists('order_items');
    }
}
