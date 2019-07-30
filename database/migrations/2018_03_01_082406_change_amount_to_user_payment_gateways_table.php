<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAmountToUserPaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_payment_gateways', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->string('real_id', 100)->after('params');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_payment_gateways', function (Blueprint $table) {
            //
        });
    }
}
