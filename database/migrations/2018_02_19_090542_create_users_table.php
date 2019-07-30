<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username', 128)->unique();
			$table->string('email', 128)->unique('email');
			$table->string('password', 128);
			$table->boolean('status')->default(0);
			$table->boolean('try_login')->default(0)->comment('How many times to change the password with invalid password from login');
			$table->boolean('try_password')->default(0)->comment('How many times to change the password with invalid password');
			$table->boolean('try_question')->default(0)->comment('How many times to answer to the security question');
			$table->string('remember_token', 100)->nullable();
			$table->string('locale', 8)->nullable();
			$table->boolean('role')->nullable()->default(1)->comment('1: freelancer, 2: buyer, 3: both, 4: super admin, 5: admin, 6: ticket manager, 7: site manager, 8: security manager');
			$table->boolean('is_auto_suspended')->nullable();
			$table->boolean('closed_reason')->default(0)->comment('1: Poor Service, 2: Irresponsive, 3: Complicated, 4: Poor Freelancers, 5: Other');
			$table->timestamps();
			$table->softDeletes();
			$table->index(['username','password']);
			$table->index(['email','password']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}

}
