<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id',false)->nullable();
            $table->string('url',100)->nullable();
            $table->string('name',100)->nullable();
            $table->integer('ordering',false)->nullable();
            $table->enum('position',['top', 'sidebar', 'both']);
            $table->string('icon',155)->nullable();
            $table->boolean('status')->default(0);
            $table->text('trans_lang')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
