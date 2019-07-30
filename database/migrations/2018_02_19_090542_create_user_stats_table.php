<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_stats', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->default(0)->index('user_id');
			$table->integer('hours')->default(0)->comment('Total Hours');
			$table->integer('last6_hours')->default(0)->comment('Total Last 6 Months Hours');
			$table->float('earning', 20)->default(0.00)->comment('Total Earning');
			$table->boolean('contracts')->nullable()->default(0)->comment('Total Closed Contracts');
			$table->boolean('open_contracts')->default(0)->comment('Total Opened Contracts');
			$table->boolean('job_success')->default(0)->comment('Job Success');
			$table->integer('jobs_posted')->default(0)->comment('Total Jobs Posted');
			$table->integer('hourly_contracts')->default(0)->comment('Total Hourly Contracts');
			$table->integer('connects')->default(30);
			$table->float('score', 10, 0)->default(0);
			$table->integer('total_portfolios')->default(0)->comment('Total Portfolios');
			$table->float('total_spent', 20)->default(0.00)->comment('Total Paid Amount');
			$table->integer('total_paid_hrs')->default(0)->comment('Total Paid Hours');
			$table->float('avg_paid_rate', 5)->default(0.00)->comment('Average Hourly Rate');
			$table->integer('total_users_suspended')->default(0)->comment('Total Jobs Suspended');
			$table->integer('total_jobs_disputed')->default(0)->comment('Total Jobs Disputed');
			$table->dateTime('last_activity')->nullable()->comment('Last Activity Time');
			$table->dateTime('connects_reset_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_stats');
	}

}
