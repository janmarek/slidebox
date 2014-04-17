<?php

namespace Presidos\Fixtures\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Presidos\User\User;

/**
 * @author Jan Marek
 */
class Users extends AbstractFixture
{

	public function load(ObjectManager $em)
	{
		$user = new User();
		$user->setName('Honza');
		$user->setEmail('honza@presidos.com');
		$user->changePassword('xxx');
		$user->allow();
		$this->addReference('user-honza', $user);
		$em->persist($user);

		$user2 = new User();
		$user2->setName('Pepa');
		$user2->setEmail('pepa@presidos.com');
		$user2->changePassword('xxx');
		$user2->allow();
		$this->addReference('user-pepa', $user2);
		$em->persist($user2);

		$user3 = new User();
		$user3->setName('Franta');
		$user3->setEmail('not@allowed.com');
		$user3->changePassword('xxx');
		$em->persist($user3);

		$em->flush();
	}

}