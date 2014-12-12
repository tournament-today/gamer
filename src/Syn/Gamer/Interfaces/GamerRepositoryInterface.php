<?php namespace Syn\Gamer\Interfaces;

use Syn\Framework\Abstracts\RepositoryInterface;

interface GamerRepositoryInterface extends RepositoryInterface
{
	/**
	 * Finds an object by nickname
	 * @param $nickname
	 * @return mixed
	 */
	public function findByNickname($nickname);

	/**
	 * Finds an object by username
	 * @param $username
	 * @return mixed
	 */
	public function findByUsername($username);

	/**
	 * Finds an object by email address
	 * @param $emailAddress
	 * @return mixed
	 */
	public function findByEmailAddress($emailAddress);

	/**
	 * Finds the last logged in users
	 * @param $number
	 * @return mixed
	 */
	public function findByLastLogin($number);

	/**
	 * Find all users using the Ip
	 * @param $ip
	 * @return array
	 */
	public function findByIp($ip);
}