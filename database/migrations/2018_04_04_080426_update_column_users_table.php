<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('currency',15)->nullable()->default('EUR')->change();
            $table->string('timezone',55)->nullable()->default('Europe/Paris')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('currency',15)->nullable()->default('EUR')->change();
            $table->string('timezone',55)->nullable()->default('Europe/Paris')->change();
        });
    }
}
