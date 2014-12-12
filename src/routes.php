<?php

Route::group(['namespace' => 'Syn'], function()
{

	/*
	 * AUTHENTICATION
	 */
	Route::any('/sign-in', [
		'as' 		=> 'sign-in',
		'uses' 		=> 'Gamer\Controllers\AuthenticationController@signIn'
	]);
	Route::any('/sign-up', [
		'as' 		=> 'sign-up',
		'uses' 		=> 'Gamer\Controllers\AuthenticationController@signUp'
	]);
	Route::group([
		'before' 	=> ['auth', 'csrf'],
	], function()
	{
		/**
		 * MODEL BINDING
		 */
		Route::model('gamer', 'Syn\Gamer\Models\Gamer');


		/**
		 * GAMER
		 */
		Route::any('/gamer/{gamer}/{name}', [
			'as'	=> 'Gamer@view',
			'uses'	=> 'Gamer\Controllers\GamerController@show',
		]);
		Route::get('/gamer/{gamer}/{name}/online', function($gamer)
		{
			\dd($gamer->isOnline);
		});
		Route::any('/gamer/{gamer}/{name}/edit', [
			'as'	=> 'Gamer@edit',
			'uses'	=> 'Gamer\Controllers\GamerController@edit',
		]);

		/**
		 * Email verification
		 */
		Route::any('/gamer/{gamer}/{name}/email-verify', [
			'as'	=> 'email-verify',
			'uses'	=> 'Gamer\Controllers\AuthenticationController@emailVerify',
		]);
		Route::get('/gamer/{gamer}/{name}/email-verified/{hash}', [
			'as'	=> 'email-verified',
			'uses'	=> 'Gamer\Controllers\AuthenticationController@emailVerified',
		]);

		Route::get('/ajax/gamer/auto-complete', [
			'as' => 'Gamer@auto-complete',
			'uses'	=> 'Gamer\Controllers\GamerController@ajaxAutoComplete',
		]);

		/**
		 * AUTHENTICATION; LOG OUT
		 */
		Route::get('/sign-out', [
			'as' 	=> 'sign-out',
			'uses' 	=> 'Gamer\Controllers\AuthenticationController@signOut'
		]);
	});
});