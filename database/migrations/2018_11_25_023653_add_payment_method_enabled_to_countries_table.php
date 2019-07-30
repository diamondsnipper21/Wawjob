<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentMethodEnabledToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->tinyInteger('paypal_enabled')->default(1)->after('bank_enabled');
            $table->tinyInteger('payoneer_enabled')->default(0)->after('bank_enabled');
            $table->tinyInteger('skrill_enabled')->default(0)->after('bank_enabled');
            $table->tinyInteger('wechat_enabled')->default(0)->after('bank_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
