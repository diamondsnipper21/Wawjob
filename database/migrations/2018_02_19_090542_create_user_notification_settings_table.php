<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserNotificationSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_notification_settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->default(0)->unique('user_id');
			$table->boolean('job_is_posted_or_modified')->default(1);
			$table->boolean('proposal_is_received')->default(1);
			$table->boolean('interview_is_accepted_or_offer_terms_are_modified')->default(1);
			$table->boolean('interview_or_offer_is_declined_or_withdrawn')->default(1);
			$table->boolean('offer_is_accepted')->default(1);
			$table->boolean('job_posting_will_expire_soon')->default(1);
			$table->boolean('job_posting_expired')->default(1);
			$table->boolean('proposal_is_submitted')->default(1);
			$table->boolean('offer_or_interview_invitation_is_received')->default(1);
			$table->boolean('offer_or_interview_invitation_is_withdrawn')->default(1);
			$table->boolean('proposal_is_rejected')->default(1);
			$table->boolean('applied_job_is_modified_or_canceled')->default(1);
			$table->boolean('hire_is_made_or_contract_begins')->default(1);
			$table->boolean('time_logging_begins')->default(1);
			$table->boolean('contract_terms_are_modified')->default(1);
			$table->boolean('contract_ends')->default(1);
			$table->boolean('timelog_is_ready_for_review')->default(1);
			$table->boolean('weekly_billing_digest')->default(1);
			$table->boolean('contract_is_going_to_be_automatically_paused')->default(1);
			$table->boolean('ijobdesk_has_tip_to_help_me_start')->default(1);
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
		Schema::dropIfExists('user_notification_settings');
	}

}
