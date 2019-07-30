<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contracts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->integer('buyer_id')->unsigned()->default(0)->index();
			$table->integer('contractor_id')->unsigned()->default(0)->index();
			$table->integer('project_id')->unsigned()->default(0)->index();
			$table->integer('application_id')->unsigned()->default(0)->index();
			$table->boolean('type')->default(0)->comment('0: Fixed, 1: Hourly');
			$table->decimal('price', 20)->default(0.00);
			$table->boolean('limit')->default(0)->comment('-1: No Limit');
			$table->boolean('is_allowed_manual_time')->default(0);
			$table->boolean('status')->default(0)->comment('0: Offer, 1: Open, 2: Paused, 3: Suspended, 8: Rejected, 9: Closed');
			$table->boolean('paused_by')->default(0)->comment('0: Client, 1: Wawjob');
			$table->dateTime('started_at')->nullable();
			$table->boolean('milestone_changed')->default(0)->comment('0: no, 1: yes, 2: accepted, 3: declined');
			$table->boolean('read_changed')->default(0)->comment('0: no, 1: yes');
			$table->boolean('closed_by')->default(0)->comment('0: by buyer, 1: by freelancer');
			$table->boolean('reason')->default(0)->comment('1: Mistake, 2: Hired Another Freelancer, 3: Irresponsive Freelancer, 4: Other');
			$table->text('closed_reason', 65535)->nullable();
			$table->integer('notification_id')->default(0)->comment('Notification id');
			$table->boolean('tracked_time')->default(0)->comment('0: Not tracked, 1: Tracked');
			$table->boolean('is_calculated')->default(0)->comment('0: Not calculated, 1: Calculated for user skill point');
			$table->boolean('buyer_need_leave_feedback')->default(0);
			$table->boolean('freelancer_need_leave_feedback')->default(0);
			$table->dateTime('ended_at')->nullable();
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
		Schema::dropIfExists('contracts');
	}

}
