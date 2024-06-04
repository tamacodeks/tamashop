<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallingCardUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calling_card_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cc_id',false)->unsigned();
            $table->dateTime('date');
            $table->integer('no_of_pins',false)->default(0);
            $table->decimal('total_amount',15,2)->default("0.00");
            $table->string('up_trans_id',55);
            $table->integer('uploaded_by',false)->unsigned()->nullable();
            $table->dateTime('uploaded_at',false)->nullable();
            $table->boolean('rollback_status')->default(0);
            $table->dateTime('rollback_at')->nullable();
            $table->integer('rollback_by',false)->unsigned()->nullable();
            $table->text('rollback_note')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();


            $table->foreign('cc_id')->references('id')->on('calling_cards');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('rollback_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calling_card_uploads');
    }
}
