<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrderIdAsNullTrackStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('track_status', function (Blueprint $table) {
            $table->integer('order_id',false)->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('track_status', function (Blueprint $table) {
            \DB::statement('UPDATE `track_status` SET `order_id` = 0 WHERE `order_id` IS NULL;');
            \DB::statement('ALTER TABLE `track_status` MODIFY `order_id` INTEGER UNSIGNED NOT NULL;');
        });
    }
}
