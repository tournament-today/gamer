<?php namespace Syn\Gamer\Models;

use App;
use Auth;
use Carbon\Carbon;
use File;
use HTML;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Syn\Framework\Abstracts\Model;
use Syn\Gamer\Interfaces\GamerInterface;
use Syn\Socket\Classes\Channel;

class Gamer extends Model implements GamerInterface, RemindableInterface, UserInterface
{
	use SoftDeletingTrait;
	use RemindableTrait;

	public $_validation = [
		'username' => ['required', 'alpha_num', 'min:4', 'max:32', 'unique:gamers,username'],
		'nick_name' => [],
		'password' => ['required', 'min:6'],
		'real_name' => ['required'],
		'email_address' => ['required', 'email', 'unique:gamers,email_address'],
		'country' => ['size:2'],
	];
	public $_validation_on_create = [
		'agree_tac' => ['required', 'accepted']
	];
	public $_types = [
		'username' => 'text',
		'nick_name' => 'text',
		'password' => 'password',
		'real_name' => 'text',
		'email_address' => 'text',
		'country' => 'text',
		'timezone' => 'select'
	];
	public $_select_values = [
		'timezone' => ['Syn\Framework\Classes\Timezone', 'getList']
	];

	protected $hidden = ['password', 'username', 'email_address', 'remember_token'];
	protected $appends = ['publishedName', 'countryFlag'];

	/**
	 * Shows a formatted version of the complete name
	 *
	 * @return mixed
	 */
	public function getPublishedNameAttribute()
	{
		$name_parts = [];

//		if($this->countryFlag)
//			$name_parts[] = HTML::image($this->countryFlag);

		if($this -> real_name)
		{
			if($this -> nick_name)
			{
				$name = explode(" ", $this -> real_name);

				foreach($name as $i => $naming)
				{
					$name_parts[] = $naming;
					// add always after first ;)
					if($i == 0)
						$name_parts[] = sprintf('\'%s\'', $this -> nick_name );
				}
				return implode(' ', $name_parts);
			}
			else
				return $this -> real_name;
		}
	}

	/**
	 * Displays the country flag of the gamer
	 *
	 * @return mixed
	 */
	public function getCountryFlagAttribute()
	{
		return $this -> country ? public_path("/media/flags/".strtolower($this->country).".png") : null;
	}

	/**
	 * Displays current Ip
	 *
	 * @return mixed
	 */
	public function getIpAttribute()
	{
		return $this -> ips() -> orderBy('date_last_visit', 'DESC') -> first();
	}

	/**
	 * Returns the fully formatted steam profile URI
	 *
	 * @return mixed
	 */
	public function getSteamUriAttribute()
	{
		// TODO: Implement getSteamUriAttribute() method.
	}

	/**
	 * Returns the parameter of an object to use in a SEO url
	 *
	 * @return mixed
	 */
	public function getLinkNameAttribute()
	{
		$name = $this -> publishedName;

		return $name ? str_replace(['/', ' ','\''], ['-','-',''], $name) : '-';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this -> email_address;
	}

	/**
	 * Whether this user is logged In
	 *
	 * @return bool
	 */
	public function getLoggedInAttribute()
	{
		return Auth::check() && Auth::user() -> id == $this -> id;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this -> id;
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this -> password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this -> remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this -> remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return "remember_token";
	}

	/**
	 * Ip's the gamer has logged in with
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function ips()
	{
		return $this -> hasMany(__NAMESPACE__.'\GamerIp');
	}

	/**
	 * Steam profile information
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function steamProfile()
	{
		return $this -> hasOne('Syn\Steam\Models\GamerSteamProfile', 'id');
	}

	/**
	 * is an admin
	 * @return bool
	 */
	public function getAdminAttribute()
	{
		foreach($this -> clans as $clan)
			if($clan -> admin && $this -> membershipOf($clan->id)->leader)
				return true;
	}

	/**
	 * Clans of the Gamer
	 */
	public function getClansAttribute()
	{
		return App::make('Syn\Clan\Interfaces\ClanRepositoryInterface') -> findByGamer($this -> id);
	}

	/**
	 * Clan member
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function memberships()
	{
		return $this -> hasMany('Syn\Clan\Models\ClanMember');
	}

	/**
	 * Whether the gamer has a membership with a certain clan
	 * @param $clan_id
	 * @return mixed
	 */
	public function membershipOf($clan_id)
	{
		$m = $this -> memberships() -> where('clan_id', $clan_id) -> first();
		return $m ? $m -> title : null;
	}

	/**
	 * Hash for seure intercom
	 * @return string
	 * @todo remove hard coded api key
	 * @obsolete at this time not used
	 */
	public function getIntercomHashAttribute()
	{
		return hash_hmac('sha256', $this -> id, '6ldC7qoPY1jBgWFQ2_cpykcBn085ygmQYMp8RIHx');
	}

	/**
	 * The invite that got this game towards our app
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function invite()
	{
		return $this -> hasOne('Syn\Clan\Models\Invite', 'gamer_invited_id');
	}

	/**
	 * All invites this gamer send
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function invites()
	{
		return $this -> hasMany('Syn\Clan\Models\Invite', 'gamer_id');
	}

	/**
	 * All invites that provided the app with gamers
	 * @return mixed
	 */
	public function getRecruitedAttribute()
	{
		return $this -> invites() -> whereNotNull('gamer_invited_id');
	}

	/**
	 * Membership in teams; invited, accepted or not
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function teamMemberships()
	{
		return $this -> hasMany('Syn\Cup\Models\Participant\Team\Member');
	}

	/**
	 * Shows all accepted team memberships
	 * @return mixed
	 */
	public function getAcceptedTeamMembershipsAttribute()
	{
		return $this -> teamMemberships() -> whereNotNull('accepted_at') -> get();
	}

	/**
	 * Loads all team memberships of cups that are running
	 * @return mixed
	 * @todo when are cups deleted? deleted_at?!
	 */
	public function getRunningTeamMembershipsAttribute()
	{
		return $this -> teamMemberships()
			-> whereHas('team', function($q)
			{
				$q -> whereHas('cup', function($cup_q)
				{

					// cups that will start within one day or have started
					$cup_q -> where('starts_at', '>=', Carbon::now() -> subDay());
				});
			})
			-> whereNotNull('accepted_at')
			-> get();
	}
	/**
	 * All open (not accepted) team memberships
	 * @return mixed
	 */
	public function getOpenTeamMembershipsAttribute()
	{
		return $this -> teamMemberships() -> whereNull('accepted_at') -> get();
	}

	/**
	 * @return string
	 */
	public function getAvatarAttribute()
	{
		if(File::exists(public_path("/media/gamers/{$this->id}.jpg")))
			return "/media/gamers/{$this->id}.jpg";

		$md5 = md5($this->email_address);
		return "//www.gravatar.com/avatar/{$md5}.png";
	}

	/**
	 * @return bool
	 */
	public function getIsOnlineAttribute()
	{
		return (bool) Channel::load('presence', 'user', $this -> id) -> occupied;
	}

	public function notifications()
	{
		return $this -> hasMany('Syn\Notification\Models\Notification', 'receiver_id') -> orderBy('id', 'DESC');
	}
	public function sentNotifications()
	{
		return $this -> hasMany('Syn\Notification\Models\Notification', 'sender_id') -> orderBy('id', 'DESC');
	}
}