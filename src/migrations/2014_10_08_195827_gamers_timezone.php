<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamersTimezone extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gamers', function($table)
		{
			$table -> string('timezone') -> nullable() -> after('country');
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
			$table -> dropColumn('timezone');
		});
	}

}
