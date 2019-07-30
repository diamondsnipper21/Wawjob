<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyInsufficientFundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_insufficient_fund', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->default(0)->unsigned()->index();
            $table->integer('user_id')->default(0)->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_insufficient_fund');
    }
}
