<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAledaProductCodeToCallingCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calling_cards', function (Blueprint $table) {
            $table->string('aleda_product_code',85)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calling_cards', function (Blueprint $table) {
            $table->dropColumn('aleda_product_code');
        });
    }
}
