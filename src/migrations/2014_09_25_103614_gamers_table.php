<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gamers', function($table)
		{
			$table -> bigIncrements('id');
			$table -> string('nick_name');
			$table -> string('real_name') -> nullable();
			$table -> string('username');
			$table -> string('password');
			$table -> string('email_address');
			$table -> string('country',2);
			$table -> string('steam_id') -> nullable();
			$table -> rememberToken();
			$table -> timestamps();
			$table -> softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gamers');
	}

}
