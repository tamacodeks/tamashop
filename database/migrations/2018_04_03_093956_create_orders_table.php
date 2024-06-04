<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('user_id',false)->unsigned();
            $table->integer('service_id',false)->unsigned();
            $table->integer('order_status_id',false)->unsigned();
            $table->integer('transaction_id',false)->unsigned()->nullable();
            $table->string('txn_ref',10)->unique();
            $table->text('comment')->nullable();
            $table->string('currency',5)->default('EUR');
            $table->decimal('public_price',15,2)->default("0.00");
            $table->decimal('buying_price',15,2)->default("0.00");
            $table->decimal('sale_margin',15,2)->default("0.00");
            $table->decimal('order_amount',15,2)->default("0.00");
            $table->decimal('shipping_charge',15,2)->default("0.00");
            $table->decimal('sur_charge',15,2)->default("0.00");
            $table->decimal('tax',15,2)->default("0.00");
            $table->decimal('grand_total',15,2)->default("0.00");
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
