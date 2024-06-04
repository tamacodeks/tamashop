<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserDefCommissionToAppCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_commissions', function (Blueprint $table) {
            $table->decimal('user_def_commission',15,2)->default("0.00")->nullable()->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_commissions', function (Blueprint $table) {
            $table->dropColumn('user_def_commission');
        });
    }
}
