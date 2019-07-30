<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractFeedbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract_feedbacks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_id')->unsigned()->index();
			$table->float('buyer_score', 8, 1)->nullable()->comment('Score buyer gets from freelancer.');
			$table->text('buyer_score_detail', 65535)->nullable();
			$table->text('buyer_feedback', 65535)->nullable()->comment('Feedback freelancer leaves to buyer.');
			$table->boolean('is_buyer_feedback_public')->default(1);
			$table->boolean('buyer_feedback_status')->default(0);
			$table->float('freelancer_score', 8, 1)->nullable();
			$table->text('freelancer_score_detail', 65535)->nullable();
			$table->text('freelancer_feedback', 65535)->nullable();
			$table->boolean('is_freelancer_feedback_public')->default(1);
			$table->boolean('freelancer_feedback_status')->default(0);
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
		Schema::dropIfExists('contract_feedbacks');
	}

}
