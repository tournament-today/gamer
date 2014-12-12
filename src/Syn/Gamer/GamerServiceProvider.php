<?php namespace Syn\Gamer;

use App;
use Auth;
use Event;
use Illuminate\Support\ServiceProvider;
use Syn\Gamer\Models\Gamer;
use Syn\Gamer\Observers\AuthObserver;
use Syn\Gamer\Observers\GamerObserver;
use Syn\Gamer\Repositories\GamerRepository;
use View;

class GamerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this -> package('syn/gamer');

		View::share('Visitor', App::make('Visitor'));
		View::share('Sentinel', App::make('Sentinel'));

		// observer for account/gamer creation etc
		Gamer::observe(new GamerObserver);
		// event listener
		Event::subscribe(new AuthObserver);

		// set Locale and time zone fixes
		// FIXME cant force locale if locale file does not exis; will force index of trans file to 0, so no plural strings will emerge
//		$locale = array_get($_SERVER, 'HTTP_ACCEPT_LANGUAGE', 'en');
//
//		if(Auth::check() && Auth::user() -> country)
//		{
//			$locale = Auth::user()->country;
//		}
//		App::setLocale($locale);
//		setlocale(LC_ALL, $locale);


		include __DIR__ . '/../../routes.php';
	}
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this -> app -> bind('Syn\Gamer\Interfaces\GamerRepositoryInterface', function()
		{
			return new GamerRepository(new Gamer);
		});

		$this -> app -> bind('Visitor', function()
		{
			return Auth::check() ? Auth::user() : new Gamer;
		});

		$this -> app -> bind('Sentinel', function()
		{
			return Gamer::where('username', 'luceos') -> first();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
