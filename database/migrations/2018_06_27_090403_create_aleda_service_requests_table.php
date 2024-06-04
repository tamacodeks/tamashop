<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAledaServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aleda_service_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('method',155);
            $table->text('payload');
            $table->string('trans_id',55)->unique();
            $table->dateTime('invoke_at');
            $table->integer('invoke_by',false)->references('id')->on('users')->nullable();
            $table->boolean('status')->default(0);
            $table->string('http_code')->nullable();
            $table->text('response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aleda_service_requests');
    }
}
