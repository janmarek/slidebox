<?php

namespace Presidos\Fixtures\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Presidos\Presentation\Theme;

/**
 * @author Jan Marek
 */
class Themes extends AbstractFixture
{

	public function load(ObjectManager $em)
	{
		$defaultTheme = new Theme();
		$defaultTheme->setName('Default');
		$defaultTheme->setClassName('theme-default');
		$this->addReference('theme-default', $defaultTheme);

		$darkTheme = new Theme();
		$darkTheme->setName('Dark');
		$darkTheme->setClassName('theme-dark');

		$em->persist($defaultTheme);
		$em->persist($darkTheme);
		$em->flush();
	}

}