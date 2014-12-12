<?php namespace Syn\Gamer\Interfaces;

interface GamerInterface
{
	/**
	 * Shows a formatted version of the complete name
	 * @return mixed
	 */
	public function getPublishedNameAttribute();

	/**
	 * Displays the country flag of the gamer
	 * @return mixed
	 */
	public function getCountryFlagAttribute();

	/**
	 * Displays current Ip
	 * @return mixed
	 */
	public function getIpAttribute();

	/**
	 * Returns the fully formatted steam profile URI
	 * @return mixed
	 */
	public function getSteamUriAttribute();

	/**
	 * Whether this user is logged In
	 * @return bool
	 */
	public function getLoggedInAttribute();
}