<?php

namespace Presidos\Fixtures\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Presidos\Fixtures\Fixtures\Themes;
use Presidos\Presentation\Presentation;

/**
 * @author Jan Marek
 */
class Presentations extends AbstractFixture implements DependentFixtureInterface
{

	public function load(ObjectManager $em)
	{
		$user = $this->getReference('user-honza');
		$user2 = $this->getReference('user-pepa');
		$theme = $this->getReference('theme-default');

		$presentation1 = new Presentation($user);
		$presentation1->setTheme($theme);
		$presentation1->setTexy(file_get_contents(__DIR__ . '/data/presentation1.texy'));
		$presentation1->setName('Presentation 1');
		$presentation1->publish();

		$presentation2 = new Presentation($user);
		$presentation2->setTheme($theme);
		$presentation2->setTexy(file_get_contents(__DIR__ . '/data/presentation2.texy'));
		$presentation2->setName('Presentation 2');
		$presentation2->setDeleted(TRUE);

		$presentation3 = new Presentation($user2);
		$presentation3->setTheme($theme);
		$presentation3->setTexy(file_get_contents(__DIR__ . '/data/presentation2.texy'));
		$presentation3->setName('Presentation 3');

		$em->persist($presentation1);
		$em->persist($presentation2);
		$em->persist($presentation3);
		$em->flush();
	}

	public function getDependencies()
	{
		return [Themes::class, Users::class];
	}

}