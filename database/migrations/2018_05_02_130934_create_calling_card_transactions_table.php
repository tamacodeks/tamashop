<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallingCardTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calling_card_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false)->unsigned();
            $table->integer('cc_id',false)->unsigned();
            $table->dateTime('date');
            $table->enum('type',['credit','debit']);
            $table->decimal('amount',15,2)->default('0.00');
            $table->decimal('debit',15,2)->default('0.00');
            $table->decimal('credit',15,2)->default('0.00');
            $table->decimal('prev_bal',15,2)->default('0.00');
            $table->decimal('balance',15,2)->default('0.00');
            $table->decimal('margin',15,2)->default('0.00');
            $table->boolean('is_exclude')->default(0);
            $table->text('description')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();

            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('calling_card_transactions');
    }
}
