<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHourlyLogMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hourly_log_maps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_id');
			$table->date('date');
			$table->smallInteger('mins')->default(0)->comment('min: 0, max: 1440');
			$table->text('act', 65535)->comment('Daily activity in JSON');
			$table->boolean('status')->default(1)->comment('1: Available, 2: Pending');
			$table->timestamps();
			$table->unique(['contract_id','date'], 'c_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hourly_log_maps');
	}

}
