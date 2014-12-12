<?php namespace Syn\Gamer\Observers;

use App;
use Carbon\Carbon;
use Request;
use Syn\Gamer\Models\GamerIp;

class AuthObserver
{
	public function onUserLogin($model)
	{
		$ip = Request::getClientIp();
		$exists = $model -> ips() -> where('ip', $ip) -> first();
		if($exists)
		{
			$exists -> date_last_visit = Carbon::now();
			// auto saves
			$exists -> increase('visits');
		}
		else
		{
			$gamer_ip = new GamerIp;
			$gamer_ip -> ip = $ip;
			$gamer_ip -> hostname = gethostbyaddr($ip);
			$gamer_ip -> date_first_visit = Carbon::now();
			$gamer_ip -> date_last_visit = Carbon::now();
			$gamer_ip -> visits = 1;
			$gamer_ip -> gamer_id = $model -> id;
			$gamer_ip -> save();
		}
	}

	public function onUserLoginAttempt($event)
	{

	}

	public function subscribe($events)
	{
		$events -> listen('auth.login', __CLASS__.'@onUserLogin');
		$events -> listen('auth.attempt', __CLASS__.'@onUserLoginAttempt');
	}
}