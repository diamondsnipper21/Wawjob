<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_points', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->default(0)->unique();
            $table->smallInteger('portrait')->default(0);
            $table->smallInteger('portfolio')->default(0);
            $table->smallInteger('certification')->default(0);
            $table->smallInteger('employment_history')->default(0);
            $table->smallInteger('education')->default(0);
            $table->smallInteger('id_verified')->default(0);
            $table->smallInteger('new_freelancer')->default(0);
            $table->float('job_success', 8, 2)->default(0);
            $table->float('last_6months', 8, 2)->default(0);
            $table->float('last_12months', 8, 2)->default(0);
            $table->float('lifetime', 8, 2)->default(0);
            $table->float('score', 8, 2)->default(0);
            $table->smallInteger('activity')->default(0);
            $table->mediumInteger('dispute')->default(0);
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
        Schema::dropIfExists('user_points');
    }
}
