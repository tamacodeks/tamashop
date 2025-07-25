<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('symbol_left', 12)->nullable();
            $table->string('symbol_right', 12)->nullable();
            $table->string('code', 3)->unique();
            $table->integer('decimal_place');
            $table->double('value', 15, 8);
            $table->string('decimal_point', 3);
            $table->string('thousand_point', 3);
            $table->boolean('status');
            $table->timestamps();
        });

        $currencies = [
            [
                'id' => 1,
                'title' => 'U.S. Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'USD',
                'decimal_place' => 2,
                'value' => 1.00000000,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 2,
                'title' => 'Euro',
                'symbol_left' => '€',
                'symbol_right' => '',
                'code' => 'EUR',
                'decimal_place' => 2,
                'value' => 0.74970001,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 3,
                'title' => 'Pound Sterling',
                'symbol_left' => '£',
                'symbol_right' => '',
                'code' => 'GBP',
                'decimal_place' => 2,
                'value' => 0.62220001,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 4,
                'title' => 'Australian Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'AUD',
                'decimal_place' => 2,
                'value' => 0.94790000,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 5,
                'title' => 'Canadian Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'CAD',
                'decimal_place' => 2,
                'value' => 0.98500001,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 6,
                'title' => 'Czech Koruna',
                'symbol_left' => '',
                'symbol_right' => 'Kč',
                'code' => 'CZK',
                'decimal_place' => 2,
                'value' => 19.16900063,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 7,
                'title' => 'Danish Krone',
                'symbol_left' => 'kr',
                'symbol_right' => '',
                'code' => 'DKK',
                'decimal_place' => 2,
                'value' => 5.59420013,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 8,
                'title' => 'Hong Kong Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'HKD',
                'decimal_place' => 2,
                'value' => 7.75290012,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 9,
                'title' => 'Hungarian Forint',
                'symbol_left' => 'Ft',
                'symbol_right' => '',
                'code' => 'HUF',
                'decimal_place' => 2,
                'value' => 221.27000427,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 10,
                'title' => 'Israeli New Sheqel',
                'symbol_left' => '?',
                'symbol_right' => '',
                'code' => 'ILS',
                'decimal_place' => 2,
                'value' => 3.73559999,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 11,
                'title' => 'Japanese Yen',
                'symbol_left' => '¥',
                'symbol_right' => '',
                'code' => 'JPY',
                'decimal_place' => 2,
                'value' => 88.76499939,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 12,
                'title' => 'Mexican Peso',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'MXN',
                'decimal_place' => 2,
                'value' => 12.63899994,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 13,
                'title' => 'Norwegian Krone',
                'symbol_left' => 'kr',
                'symbol_right' => '',
                'code' => 'NOK',
                'decimal_place' => 2,
                'value' => 5.52229977,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 14,
                'title' => 'New Zealand Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'NZD',
                'decimal_place' => 2,
                'value' => 1.18970001,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 15,
                'title' => 'Philippine Peso',
                'symbol_left' => 'Php',
                'symbol_right' => '',
                'code' => 'PHP',
                'decimal_place' => 2,
                'value' => 40.58000183,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 16,
                'title' => 'Polish Zloty',
                'symbol_left' => '',
                'symbol_right' => 'zł',
                'code' => 'PLN',
                'decimal_place' => 2,
                'value' => 3.08590007,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 17,
                'title' => 'Singapore Dollar',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'SGD',
                'decimal_place' => 2,
                'value' => 1.22560000,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 18,
                'title' => 'Swedish Krona',
                'symbol_left' => 'kr',
                'symbol_right' => '',
                'code' => 'SEK',
                'decimal_place' => 2,
                'value' => 6.45870018,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 19,
                'title' => 'Swiss Franc',
                'symbol_left' => 'CHF',
                'symbol_right' => '',
                'code' => 'CHF',
                'decimal_place' => 2,
                'value' => 0.92259997,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 20,
                'title' => 'Taiwan New Dollar',
                'symbol_left' => 'NT$',
                'symbol_right' => '',
                'code' => 'TWD',
                'decimal_place' => 2,
                'value' => 28.95199966,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 21,
                'title' => 'Thai Baht',
                'symbol_left' => '฿',
                'symbol_right' => '',
                'code' => 'THB',
                'decimal_place' => 2,
                'value' => 30.09499931,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 22,
                'title' => 'Ukrainian hryvnia',
                'symbol_left' => '₴',
                'symbol_right' => '',
                'code' => 'UAH',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 23,
                'title' => 'Icelandic króna',
                'symbol_left' => 'kr',
                'symbol_right' => '',
                'code' => 'ISK',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 24,
                'title' => 'Croatian kuna',
                'symbol_left' => 'kn',
                'symbol_right' => '',
                'code' => 'HRK',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 25,
                'title' => 'Romanian leu',
                'symbol_left' => 'lei',
                'symbol_right' => '',
                'code' => 'RON',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 26,
                'title' => 'Bulgarian lev',
                'symbol_left' => 'лв.',
                'symbol_right' => '',
                'code' => 'BGN',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 27,
                'title' => 'Turkish lira',
                'symbol_left' => '₺',
                'symbol_right' => '',
                'code' => 'TRY',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 28,
                'title' => 'Chilean peso',
                'symbol_left' => '$',
                'symbol_right' => '',
                'code' => 'CLP',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 29,
                'title' => 'South African rand',
                'symbol_left' => 'R',
                'symbol_right' => '',
                'code' => 'ZAR',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 30,
                'title' => 'Brazilian real',
                'symbol_left' => 'R$',
                'symbol_right' => '',
                'code' => 'BRL',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 31,
                'title' => 'Malaysian ringgit',
                'symbol_left' => 'RM',
                'symbol_right' => '',
                'code' => 'MYR',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 32,
                'title' => 'Russian ruble',
                'symbol_left' => '₽',
                'symbol_right' => '',
                'code' => 'RUB',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 33,
                'title' => 'Indonesian rupiah',
                'symbol_left' => 'Rp',
                'symbol_right' => '',
                'code' => 'IDR',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 34,
                'title' => 'Indian rupee',
                'symbol_left' => '₹',
                'symbol_right' => '',
                'code' => 'INR',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 35,
                'title' => 'Korean won',
                'symbol_left' => '₩',
                'symbol_right' => '',
                'code' => 'KRW',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 36,
                'title' => 'Renminbi',
                'symbol_left' => '¥',
                'symbol_right' => '',
                'code' => 'CNY',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 37,
                'title' => 'Special drawing rights',
                'symbol_left' => '',
                'symbol_right' => '',
                'code' => 'XDR',
                'decimal_place' => 2,
                'value' => 0.00,
                'decimal_point' => '.',
                'thousand_point' => ',',
                'status' => 1,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
