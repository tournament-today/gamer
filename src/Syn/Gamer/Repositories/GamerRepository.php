<?php namespace Syn\Gamer\Repositories;


use Syn\Framework\Abstracts\Repository;
use Syn\Gamer\Interfaces\GamerRepositoryInterface;

class GamerRepository extends Repository implements GamerRepositoryInterface
{

	/**
	 * Finds an object by nickname
	 *
	 * @param $nickname
	 * @return mixed
	 */
	public function findByNickname($nickname)
	{
		// TODO: Implement findByNickname() method.
	}

	/**
	 * Finds an object by username
	 *
	 * @param $username
	 * @return mixed
	 */
	public function findByUsername($username)
	{
		// TODO: Implement findByUsername() method.
	}

	/**
	 * Finds an object by email address
	 *
	 * @param $emailAddress
	 * @return mixed
	 */
	public function findByEmailAddress($emailAddress)
	{
		// TODO: Implement findByEmailAddress() method.
	}

	/**
	 * Finds the last logged in users
	 *
	 * @param $number
	 * @return mixed
	 */
	public function findByLastLogin($number)
	{
		// TODO: Implement findByLastLogin() method.
	}

	/**
	 * Find all users using the Ip
	 *
	 * @param $ip
	 * @return array
	 */
	public function findByIp($ip)
	{
		// TODO: Implement findByIp() method.
	}

	/**
	 * Returns a query builder filtering on what is viewable
	 *
	 * @param $id
	 * @return mixed
	 */
	public function isViewable($id)
	{
		// TODO: Implement isViewable() method.
	}

	/**
	 * Returns a query builder filtering on what is editable
	 *
	 * @param $id
	 * @return mixed
	 */
	public function isEditable($id)
	{
		// TODO: Implement isEditable() method.
	}

	/**
	 * Returns a query builder filtering on what is deletable
	 *
	 * @param $id
	 * @return mixed
	 */
	public function isDeletable($id)
	{
		// TODO: Implement isDeletable() method.
	}

	public function findLike($find)
	{
		$find = e($find);
		return $this -> model
			-> with('memberships')
			-> where('nick_name', 'like', "%%{$find}%%")
			-> orWhere('real_name', 'like', "%%{$find}%%")
			-> paginate(10);
	}
}