<?php namespace Syn\Gamer\Tasks;


use Mail;
use Syn\Gamer\Models\Gamer;

class VerifyEmailAddressTask
{
	public function send($job, $data)
	{
		$gamer_id = array_get($data, 'id');

		$gamer = Gamer::find($gamer_id);

		if(!$gamer)
			return $job -> delete;

		Mail::send('e-mail.verify_email_address',compact('gamer'), function($message) use ($gamer)
		{
			$message -> to($gamer -> email_address, $gamer -> publishedName) -> subject("Verify e-mail address");
		});

		$job -> delete();
	}
}