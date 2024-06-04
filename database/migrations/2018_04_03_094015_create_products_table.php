<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id',false)->unsigned();
            $table->integer('category_id',false)->unsigned();
            $table->integer('stock_status_id',false)->unsigned();
            $table->string('name',255);
            $table->text('description')->nullable();
            $table->text('tags')->nullable();
            $table->string('image',255)->nullable();
            $table->decimal('cost',15,2)->default("0.00");
            $table->decimal('price',15,2)->default("0.00");
            $table->decimal('own_price',15,2)->default("0.00");
            $table->decimal('reseller_price',15,2)->default("0.00");
            $table->boolean('free_shipping')->default(0);
            $table->decimal('shipping_charge',15,2)->default("0.00");
            $table->decimal('sur_charge',15,2)->default("0.00");
            $table->text('sur_charge_desc')->nullable();
            $table->date('date_available')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_track_qty')->default(0);
            $table->integer('track_qty',false)->default(0);
            $table->integer('min_to_order',false)->default(1);
            $table->integer('max_to_order',false)->default(1);
            $table->text('trans_lang')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->foreign('stock_status_id')->references('id')->on('stock_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
