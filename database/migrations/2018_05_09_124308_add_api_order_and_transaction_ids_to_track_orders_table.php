<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiOrderAndTransactionIdsToTrackOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('track_orders', function (Blueprint $table) {
            $table->string('api_order_id',55)->nullable()->after('order_status_id');
            $table->string('api_trans_id',55)->nullable()->after('api_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('track_orders', function (Blueprint $table) {

            $table->dropColumn('api_order_id');
            $table->dropColumn('api_trans_id');
        });
    }
}
