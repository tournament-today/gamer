<?php namespace Syn\Gamer\Controllers;

use App;
use Auth;
use Input;
use Mail;
use Queue;
use Redirect;
use Syn\Clan\Models\Invite;
use Syn\Framework\Abstracts\Controller;
use Syn\Gamer\Models\Gamer;
use Syn\Notification\Models\Notification;

class AuthenticationController extends Controller
{
	/**
	 * Signs a user in for the website
	 * @return \Illuminate\Http\RedirectResponse|mixed
	 */
	public function signIn()
	{
		if(Auth::check())
			return Redirect::home();

		// handle post request
		$return = $this -> onRequestMethod("post", function()
		{
			$credentials = Input::only(['username','password']);
			// support logging in with e-mail address or username
			if(filter_var($credentials['username'], FILTER_VALIDATE_EMAIL))
			{
				$credentials['email_address'] = $credentials['username'];
				unset($credentials['username']);
			}
			// attempt login
			if(Auth::attempt($credentials, Input::get('remember_me', false)))
				return Redirect::intended(route('home'));
			else
				return Redirect::route('sign-in') -> with('login_failed', trans('sign-in.login-failed'));
		});
		return $return ?: $this -> view('pages.forms.sign-in');
	}

	/**
	 * Signs a user up for our website
	 * @return mixed
	 */
	public function signUp()
	{

		$visitor = $this -> getVisitor();

		$return = $this -> onRequestMethod('post', function() use ($visitor)
		{

			$validator = $visitor -> getValidator();
			if($validator -> fails())
				return Redirect::route('sign-up') ->withInput() -> withErrors($validator);

			$visitor -> unguard();
			$visitor -> fill(Input::only(['username', 'real_name', 'password', 'email_address']));
			$visitor -> save();
			$visitor -> reguard();
			Auth::login($visitor);
			// send an e-mail
			Queue::push('Syn\Gamer\Tasks\VerifyEmailAddressTask@send', [
				'id' => $visitor -> id
			]);
			// if using a invite code
			if(Input::has('invite'))
			{
				$invite = Invite::where('token', Input::get('invite')) -> first();
				if($invite && $invite -> usable && $visitor -> email_address == $invite -> email_address)
				{
					$invite -> useFor();
				}
			}
			return $this -> redirect('home');
		});
		return $return ?: $this -> view('pages.forms.sign-up');
	}

	public function emailVerify($gamer, $name)
	{
		$visitor = $this -> getVisitor();

		if(!$gamer)
			return $this -> notFound("Gamer not found");

		if((!$visitor->admin && $gamer -> id != $visitor->id) || $gamer->email_verified)
			return $this->redirect('home');

		// send an e-mail
		Queue::push('Syn\Gamer\Tasks\VerifyEmailAddressTask@send', [
			'id' => $gamer -> id
		]);

		$notification = new Notification;
		$notification -> unguard();
		$notification -> fill([
			'title' => trans('gamer.email-verification-send'),
			'receiver_id' => $gamer -> id,
			'sender_id' => $visitor->id
		]);
		$notification -> save();

		return $this -> redirect('back');
	}
	public function emailVerified($gamer, $name, $hash)
	{
		$visitor = $this -> getVisitor();
		if($visitor->id != $gamer->id ||
			$visitor -> email_verified ||
			md5($visitor->email_address) != $hash)
			return $this->redirect('home');

		$visitor->email_verified = true;
		$visitor->save();

		$notification = new Notification;
		$notification -> unguard();
		$notification -> fill([
			'title' => trans('gamer.email-verification-done'),
			'receiver_id' => $gamer -> id,
			'sender_id' => $visitor->id
		]);
		$notification -> save();

		return $this->redirect('home');
	}
	/**
	 * Sign a user out from the website
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function signOut()
	{
		Auth::logout();
		return $this -> redirect('home');
	}
}