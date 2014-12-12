<?php namespace Syn\Gamer\Observers;

use Hash;
use Queue;
use Syn\Notification\Classes\HipChat;

class GamerObserver
{
	public function created($model)
	{
		Queue::push(function($job) use ($model)
		{
			HipChat::messageRoom("Signed up: {$model->publishedName} ({$model->email_address})");
			$job->delete();
		});
	}
	public function saving($model)
	{
		if($model -> isDirty('password'))
			$model -> password = Hash::make($model -> password);

//		Queue::push(function($job) use ($model)
//		{
//			HipChat::messageRoom("Account changed: {$model->publishedName} ({$model->email_address})");
//			$job->delete();
//		});
	}
}