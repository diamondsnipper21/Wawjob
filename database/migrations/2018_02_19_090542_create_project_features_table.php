<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectFeaturesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_features', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('project_id')->unsigned()->index();
			$table->boolean('freelancer_type')->default(0);
			$table->string('feedback_score')->nullable();
			$table->integer('hours_billed')->unsigned()->default(0);
			$table->string('location', 256)->nullable();
			$table->boolean('en_level')->default(0);
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
		Schema::dropIfExists('project_features');
	}

}
