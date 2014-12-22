<?php namespace Syn\Gamer\Controllers;

use App, Input, Redirect, Auth;
use Illuminate\Support\Collection;
use Request;
use Syn\Framework\Abstracts\Controller;
use Syn\Gamer\Interfaces\GamerRepositoryInterface;
use Syn\Gamer\Models\Gamer;
use Syn\Steam\Classes\OpenIdAuthentication;
use Validator;

class GamerController extends Controller
{
	public function __construct(GamerRepositoryInterface $gamer)
	{
		$this -> gamer = $gamer;
	}

	/**
	 * @param \Syn\Gamer\Models\Gamer $gamer
	 * @param null                    $name
	 * @return mixed
	 */
	public function show(Gamer $gamer, $name = null)
	{
		$this -> icon = 'account';
		$this -> title = $gamer -> publishedName;
		return $this -> view('pages.gamer.show', compact('gamer'));
	}

	/**
	 * @param \Syn\Gamer\Models\Gamer $gamer
	 * @param null                    $name
	 * @return mixed
	 */
	public function edit(Gamer $gamer, $name = null)
	{
		if(!$gamer -> allowEdit())
			return $this -> notAllowed('Edit account', 'Insufficient rights');

		$this -> icon = 'edit';
		$this -> title = trans('generic.edit');
		return $this -> onRequestMethod("post", function() use ($gamer)
		{
			if(Input::has('edit-steam'))
				return $this -> editSteam($gamer);
			elseif(Input::has('username'))
				return $this -> editProfile($gamer, Input::only(['real_name','username','nick_name','country','email_address','timezone']));
			elseif(Input::has('edit-password'))
				return $this -> editPassword($gamer, Input::only(['password','password_repeat']));
		}) ?: $this -> view('pages.gamer.edit', compact('gamer'));
	}

	/**
	 * Updates password
	 * @param $gamer
	 * @param $input
	 * @return mixed
	 */
	protected function editPassword($gamer, $input)
	{
		$validator = Validator::make($input, [
			'password' => array_get($gamer -> _validation,'password'),
			'password_repeat' => ['same:password']
		]);
		if($validator -> fails())
			return $gamer -> redirectEdit -> withInput() -> withErrors($validator);

		$gamer -> password = array_get($input, 'password');
		$gamer -> save();

		return $gamer -> redirectShow;
	}

	/**
	 * Edit steam URL
	 *
	 * @param $gamer
	 * @return mixed
	 */
	protected function editSteam($gamer)
	{
		$auth = new OpenIdAuthentication($gamer);
		return $auth->begin();
	}

	/**
	 * Edits profile details
	 * @param       $gamer
	 * @param array $input
	 * @return bool
	 */
	protected function editProfile($gamer, $input = [])
	{
		if(!count($input))
			return false;

		$validator = $gamer -> getValidator($input, array_keys($input));


		if($validator -> fails())
			return $gamer -> redirectEdit ->withInput() -> withErrors($validator);

		// save the changes
		$gamer -> unguard();
		$gamer -> fill($input);

		$gamer -> save();
		$gamer -> reguard();
		return $gamer -> redirectShow;
	}

	public function ajaxAutoComplete()
	{
		if(!Request::ajax())
			App::abort(403);

		$find = Input::get('find');
		$page = Input::get('page', 1);

		if(empty($find))
			return $this -> json(['more' => false, 'items' => []]);

		$set = [];
		foreach($this->gamer->findLike($find) as $gamer)
		{
			$set[] = [
				'id' => $gamer -> id,
				'text' => $gamer->publishedName
			];
		}
		return $this->json([
			'more' => false,
			'items' => $set
		]);
	}
}