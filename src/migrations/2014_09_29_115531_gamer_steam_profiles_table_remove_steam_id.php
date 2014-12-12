<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamerSteamProfilesTableRemoveSteamId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('gamers', function($table)
		{
			$table -> dropColumn('steam_id');
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
			$table -> integer('steam_id') -> nullable();
		});
	}

}
