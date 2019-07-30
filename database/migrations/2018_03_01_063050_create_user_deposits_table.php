<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id')->default(0);
            $table->boolean('gateway')->default(1)->index('gateway')->comment('1: Paypal, 2: Credit Card, 3: Weixin, 4: Wire transfer, 5: Skrill');
            $table->decimal('amount', 20)->unsigned()->default(0.00);
            $table->string('real_id')->comment('Real gateway id from third party');
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
        Schema::dropIfExists('user_deposits');
    }
}
