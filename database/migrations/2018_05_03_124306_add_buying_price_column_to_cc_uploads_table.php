<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuyingPriceColumnToCcUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calling_card_uploads', function (Blueprint $table) {
            $table->decimal('buying_price',15,2)->default("0.00")->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calling_card_uploads', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });
    }
}
