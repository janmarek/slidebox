<?php

namespace Presidos\User;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;

class PasswordAuthenticator
{

	private $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param string $mail
	 * @param string $password
	 * @return User
	 */
	public function authenticate($mail, $password)
	{
		$user = $this->userRepository->findAllowedByEmail($mail);

		if (!$user) {
			throw new AuthenticationException("User '$mail' not found.", IAuthenticator::IDENTITY_NOT_FOUND);
		}

		if (!$user->checkPassword($password)) {
			throw new AuthenticationException("Invalid password.", IAuthenticator::INVALID_CREDENTIAL);
		}

		return $user;
	}

}
