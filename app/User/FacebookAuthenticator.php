<?php

namespace Presidos\User;

use Doctrine\ORM\EntityManager;

class FacebookAuthenticator
{

	private $userRepository;

	private $em;

	public function __construct(UserRepository $userRepository, EntityManager $em)
	{
		$this->userRepository = $userRepository;
		$this->em = $em;
	}

	/**
	 * @param array $fbUser
	 * @return User
	 */
	public function authenticate(array $fbUser)
	{
		$user = $this->userRepository->findOneByFacebookUid($fbUser['id']);

		if (!$user) {
			$user = $this->register($fbUser);
		}

		return $user;
	}

	public function register(array $me)
	{
		$user = new User();
		$user->setName($me['name']);
		$user->setEmail($me['email']);
		$user->setFacebookUid($me['id']);
		$user->allow();

		if (!$this->userRepository->hasUniqueEmail($user)) {
			throw new DuplicateEmailException('User with email ' . $user->getEmail() . ' is already registered.');
		}

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

}
