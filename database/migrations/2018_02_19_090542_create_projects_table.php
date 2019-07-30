<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('category_id')->unsigned()->index();
			$table->integer('client_id')->unsigned()->index();
			$table->string('subject');
			$table->text('desc', 65535);
			$table->boolean('type')->default(0)->comment('0: Fixed, 1: Hourly');
			$table->boolean('term')->nullable()->comment('0: Not sure, 1: One-time, 2: Ongoing');
			$table->boolean('experience_level')->default(0)->comment('0: Entry, 1: Intermediate, 2: Expert');
			$table->boolean('affordable_rate')->default(0)->comment('0: Not sure, 1: /hr and below, 2: /hr - /hr, 3: /hr - /hr, 4: /hr and above');
			$table->string('duration', 10);
			$table->string('workload', 10)->comment('NS: Not sure, MT: More than 30hrs, LT: Less than 30hrs');
			$table->decimal('price', 20);
			$table->boolean('req_cv')->default(1);
			$table->boolean('qualification_success_score')->default(0);
			$table->smallInteger('qualification_hours')->unsigned()->default(0);
			$table->string('qualification_location', 50)->nullable();
			$table->boolean('contract_limit')->default(1)->comment('0: More than one, 1: One');
			$table->boolean('is_public')->default(1)->comment('0: Private, 1: Public');
			$table->boolean('is_featured')->default(0)->comment('0: Not featured, 1: Featured');
			$table->boolean('status')->default(1)->comment('0: Closed, 1: Open');
			$table->string('reason', 256);
			$table->text('cancelled_reason', 65535)->nullable();
			$table->integer('cancelled_by')->nullable();
			$table->boolean('accept_term')->default(0)->comment('0: Not accepted, 1: Accepted');
			$table->dateTime('cancelled_at')->nullable();
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
		Schema::dropIfExists('projects');
	}

}
