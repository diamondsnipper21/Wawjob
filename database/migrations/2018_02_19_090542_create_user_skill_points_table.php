<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSkillPointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_skill_points', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->default(0)->index('user_id');
			$table->float('c_laravel', 10, 0)->default(0);
			$table->float('c_magento', 10, 0)->default(0);
			$table->float('c_mysql', 10, 0)->default(0);
			$table->float('c_php', 10, 0)->default(0);
			$table->float('c_ruby', 10, 0)->default(0);
			$table->float('c_wordpress', 10, 0)->default(0);
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
		Schema::dropIfExists('user_skill_points');
	}

}
