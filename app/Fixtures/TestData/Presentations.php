<?php

namespace SlideBox\Fixtures\TestData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SlideBox\Fixtures\Fixtures\Themes;
use SlideBox\Presentation\Presentation;

/**
 * @author Jan Marek
 */
class Presentations extends AbstractFixture implements DependentFixtureInterface
{

	public function load(ObjectManager $em)
	{
		$honza = $this->getReference('user-honza');
		$pepa = $this->getReference('user-pepa');
		$petr = $this->getReference('user-petr');
		$theme = $this->getReference('theme-classic-orange');

		$presentation1 = new Presentation($honza);
		$presentation1->setThemeVariant($theme);
		$presentation1->setTexy(file_get_contents(__DIR__ . '/data/presentation1.texy'));
		$presentation1->setName('Presentation 1');
		$presentation1->setCollaborators([$petr]);
		$presentation1->publish();

		$presentation2 = new Presentation($honza);
		$presentation2->setThemeVariant($theme);
		$presentation2->setTexy(file_get_contents(__DIR__ . '/data/presentation2.texy'));
		$presentation2->setName('Presentation 2');
		$presentation2->setDeleted(TRUE);

		$presentation3 = new Presentation($pepa);
		$presentation3->setThemeVariant($theme);
		$presentation3->setTexy(file_get_contents(__DIR__ . '/data/presentation3.texy'));
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