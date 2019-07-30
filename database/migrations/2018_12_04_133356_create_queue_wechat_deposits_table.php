<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueWechatDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_wechat_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->decimal('amount', 20)->default(0.00);
            $table->decimal('original_amount', 20)->default(0.00);
            $table->integer('user_payment_gateway_id')->unsigned()->default(0)->index();
            $table->tinyInteger('status')->default(0)->comment('0: Waiting for QR code, 1: Waiting for payment, 2: Approved payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_wechat_deposits');
    }
}
