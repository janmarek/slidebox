<?php

namespace SlideBox\Fixtures\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SlideBox\User\User;

/**
 * @author Jan Marek
 */
class Users extends AbstractFixture
{

	public function load(ObjectManager $em)
	{
		$honza = new User();
		$honza->setName('Honza');
		$honza->setEmail('honza@slidebox.com');
		$honza->changePassword('xxx');
		$honza->allow();
		$this->addReference('user-honza', $honza);
		$em->persist($honza);

		$pepa = new User();
		$pepa->setName('Pepa');
		$pepa->setEmail('pepa@slidebox.com');
		$pepa->changePassword('xxx');
		$pepa->allow();
		$this->addReference('user-pepa', $pepa);
		$em->persist($pepa);

		$franta = new User();
		$franta->setName('Franta');
		$franta->setEmail('not@allowed.com');
		$franta->changePassword('xxx');
		$em->persist($franta);

		$petr = new User();
		$petr->setName('Petr');
		$petr->setEmail('petr@slidebox.com');
		$petr->changePassword('xxx');
		$petr->allow();
		$this->addReference('user-petr', $petr);
		$em->persist($petr);

		$em->flush();
	}

}