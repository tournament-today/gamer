<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamerIpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gamer_ips', function($table)
		{
			$table -> bigIncrements('id');
			$table -> bigInteger('gamer_id') -> unsigned();
			$table -> string('ip');
			$table -> string('hostname');
			$table -> timestamp('date_first_visit');
			$table -> timestamp('date_last_visit');
			$table -> integer('visits') -> default(1);

			$table -> foreign('gamer_id') -> references('id') -> on('gamers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gamer_ips');
	}

}
