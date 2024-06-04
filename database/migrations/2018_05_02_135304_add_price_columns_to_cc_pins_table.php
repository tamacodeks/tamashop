<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceColumnsToCcPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calling_card_pins', function (Blueprint $table) {
            $table->decimal('face_value',15,2)->default("0.00")->nullable()->after('serial');
            $table->decimal('buying_price',15,2)->default("0.00")->nullable()->after('face_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calling_card_pins', function (Blueprint $table) {
            $table->dropColumn('buying_price');
            $table->dropColumn('face_value');
        });
    }
}
