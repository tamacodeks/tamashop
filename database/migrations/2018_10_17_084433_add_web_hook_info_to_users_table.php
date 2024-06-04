<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebHookInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('web_hook_url')->after('remember_token')->nullable();
            $table->string('web_hook_uri')->after('web_hook_url')->nullable();
            $table->string('web_hook_token')->after('web_hook_uri')->nullable();
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
            $table->dropColumn('web_hook_url');
            $table->dropColumn('web_hook_uri');
            $table->dropColumn('web_hook_token');
        });
    }
}
