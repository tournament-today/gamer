<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamersEmailValidatedColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gamers', function($table)
		{
			$table -> boolean('email_verified') -> default(false) -> after('email_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('gamers', function($table)
		{
			$table -> dropColumn('email_verified');
		});
	}

}
