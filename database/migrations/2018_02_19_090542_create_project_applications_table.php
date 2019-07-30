<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_applications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('project_id')->unsigned()->default(0)->index();
			$table->integer('user_id')->unsigned()->default(0)->index();
			$table->boolean('provenance')->default(0);
			$table->smallInteger('type')->unsigned()->default(0);
			$table->decimal('price', 20);
			$table->text('cv', 65535)->nullable();
			$table->string('duration', 10)->nullable();
			$table->boolean('status')->default(0);
			$table->integer('project_invitation_id')->default(0)->index('project_invitation_id');
			$table->boolean('is_declined')->default(0)->comment('1: Freelancer declined, 2: Buyer declined');
			$table->boolean('is_archived')->default(0)->comment('0: Unarchived, 1: Archived');
			$table->boolean('is_featured')->default(0);
			$table->boolean('is_checked')->default(0)->comment('Detect if checked by buyer');
			$table->boolean('is_liked')->default(0)->comment('0: Normal, 1: Liked, -1: Disliked');
			$table->string('memo')->nullable()->comment('Memo written by buyer');
			$table->boolean('decline_reason')->default(0);
			$table->text('reason', 65535)->nullable();
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
		Schema::dropIfExists('project_applications');
	}

}
