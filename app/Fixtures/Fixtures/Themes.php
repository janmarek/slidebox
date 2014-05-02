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
		$orangeTheme = new Theme();
		$orangeTheme->setName('Orange');
		$orangeTheme->setClassName('theme-orange');
		$this->addReference('theme-orange', $orangeTheme);
		$em->persist($orangeTheme);

		$darkTheme = new Theme();
		$darkTheme->setName('Dark');
		$darkTheme->setClassName('theme-dark');
		$em->persist($darkTheme);

		$limeTheme = new Theme();
		$limeTheme->setName('Lime');
		$limeTheme->setClassName('theme-lime');
		$em->persist($limeTheme);

		$paperTheme = new Theme();
		$paperTheme->setName('Paper');
		$paperTheme->setClassName('theme-paper');
		$em->persist($paperTheme);

		$blueTheme = new Theme();
		$blueTheme->setName('Blue');
		$blueTheme->setClassName('theme-blue');
		$em->persist($blueTheme);

		$lightBlueTheme = new Theme();
		$lightBlueTheme->setName('Light Blue');
		$lightBlueTheme->setClassName('theme-light-blue');
		$em->persist($lightBlueTheme);

		$redTheme = new Theme();
		$redTheme->setName('Red');
		$redTheme->setClassName('theme-red');
		$em->persist($redTheme);

		$plainTheme = new Theme();
		$plainTheme->setName('Plain');
		$plainTheme->setClassName('theme-plain');
		$em->persist($plainTheme);

		$plainTheme = new Theme();
		$plainTheme->setName('Banana');
		$plainTheme->setClassName('theme-banana');
		$em->persist($plainTheme);

		$plainTheme = new Theme();
		$plainTheme->setName('Forest');
		$plainTheme->setClassName('theme-forest');
		$em->persist($plainTheme);

		$em->flush();
	}

}