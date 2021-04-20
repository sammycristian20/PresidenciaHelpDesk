<?php

namespace App\Exceptions;

use App\User;

/**
 * Custom exception class for verification required check
 *
 * @package App\Exceptions
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 */
class VerificationRequiredException extends CustomException
{
	/**
	 * @var string
	 */
	private $redirectURL = '';

	/**
	 * @var string
	 */
	private $requiredEntity = '';

	/**
	 * @var User
	 */
	private $user;

	/**
	 * Constructor method sets values of class properties
	 * @param string  $url     URL to redirect user if entity is not verified
	 * @param string  $entity  entity which is not verified
	 * @param User    $user    User with unverified entity
	 */
	public function __construct(string $url, string $entity, User $user) {
		$this->redirectURL = $url;
		$this->requiredEntity = $entity;
		$this->user = $user;
		parent::__construct();
	}

	/**
	 * Method return redirect URL as string
	 * @return string
	 */
	public function getRedirectURL():string
	{
		return $this->redirectURL;
	}

	/**
	 * Method return required entity as string
	 * @return string
	 */
	public function getRequiredEntity():string
	{
		return $this->requiredEntity;
	}

	/**
	 * Method returns user who tried to login
	 * @return User
	 */
	public function getLoginUser():User
	{
		return $this->user;
	}
}
