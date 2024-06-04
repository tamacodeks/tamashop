<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id',false)->unsigned();
            $table->string('cust_id',10)->unique();
            $table->integer('parent_id',false)->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name',55);
            $table->string('last_name',55)->nullable();
            $table->string('mobile',15)->nullable();
            $table->string('email',85)->nullable();
            $table->boolean('status')->default(1);
            $table->string('currency',15)->default('EUR');
            $table->string('timezone',55)->default('Europe/Paris');
            $table->string('image',255)->nullable();
            $table->integer('country_id',false)->unsigned()->nullable();
            $table->text('address')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->boolean('pin_print_again')->default(1);
            $table->boolean('can_process_order')->default(0);
            $table->rememberToken();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by',false)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by',false)->nullable();

            $table->foreign('group_id')->references('id')->on('user_groups');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
