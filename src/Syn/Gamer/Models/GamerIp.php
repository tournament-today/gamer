<?php namespace Syn\Gamer\Models;

use Syn\Framework\Abstracts\Model;

class GamerIp extends Model
{
	public $timestamps = false;

	public function getDates()
	{
		return ['date_first_visit', 'date_last_visit'];
	}

	public function gamer()
	{
		return $this -> belongsTo('Syn\Gamer\Model\Gamer');
	}

	public function getRelatedAttribute()
	{
		$ip = $this -> ip;
		$hostname = $this -> hostname;

		return Gamer::where('id', '!=', $this->gamer_id)
			->where(function($qwhere) use ($ip, $hostname)
			{
				$qwhere->whereHas('ips', function($q) use ($ip)
				{
					$q -> where('ip', $ip);
				})
				-> orWhereHas('ips', function($q) use ($hostname)
				{
					$q -> where('hostname', $hostname);
				});
			})
			-> get();
	}
}