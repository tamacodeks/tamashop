<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMgrComAndRetailerComToAppCommissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_commissions', function (Blueprint $table) {
            $table->decimal('mgr_def_com',15,2)->nullable()->after('user_def_commission');
            $table->decimal('retailer_def_com',15,2)->nullable()->after('mgr_def_com');
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
            $table->dropColumn('mgr_def_com');
            $table->dropColumn('retailer_def_com');
        });
    }
}
