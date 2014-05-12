<?php

namespace Presidos\Fixtures\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Presidos\Presentation\Theme;
use Presidos\Presentation\ThemeVariant;

/**
 * @author Jan Marek
 */
class Themes extends AbstractFixture
{

	public function load(ObjectManager $em)
	{
		$defaultTheme = new Theme('Classic', 'theme-classic');
		$orangeVariant = $defaultTheme->addVariant('Orange', 'variant-classic-orange', 'ff681f');
		$this->addReference('theme-classic-orange', $orangeVariant);
		$defaultTheme->addVariant('Blue', 'variant-classic-blue', '3483D2');
		$defaultTheme->addVariant('Green', 'variant-classic-green', '3b741d');
		$defaultTheme->addVariant('Red', 'variant-classic-red', 'ae0006');
		$defaultTheme->addVariant('Violet', 'variant-classic-violet', '43229e');
		$defaultTheme->addVariant('Azure', 'variant-classic-azure', '028092');
		$em->persist($defaultTheme);

		$darkTheme = new Theme('Dark', 'theme-dark');
		$darkTheme->addVariant('Blue', 'variant-dark-blue', '2c576f');
		$darkTheme->addVariant('Green', 'variant-dark-green', '156f40');
		$darkTheme->addVariant('Brown', 'variant-dark-brown', '795729');
		$em->persist($darkTheme);

		$pastelTheme = new Theme('Pastel', 'theme-pastel');
		$pastelTheme->addVariant('Lime', 'variant-pastel-lime', 'c8ff8b');
		$pastelTheme->addVariant('Blue', 'variant-pastel-blue', 'd1e8ff');
		$pastelTheme->addVariant('Banana', 'variant-pastel-banana', 'fff38f');
		$em->persist($pastelTheme);

		$plainTheme = new Theme('Plain', 'theme-plain');
		$plainTheme->addVariant('Blue', 'variant-plain-blue', '2b5288');
		$plainTheme->addVariant('Green', 'variant-plain-green', '397335');
		$plainTheme->addVariant('White', 'variant-plain-white', 'ffffff');
		$plainTheme->addVariant('Black', 'variant-plain-black', '000000');
		$plainTheme->addVariant('Violet', 'variant-plain-violet', '43229e');
		$em->persist($plainTheme);

		$paperTheme = new Theme('Paper', 'theme-paper');
		$paperTheme->addVariant('Beige', 'variant-paper-beige', 'ebdfc9');
		$paperTheme->addVariant('Gray', 'variant-paper-gray', 'bfd1dd');
		$em->persist($paperTheme);

		$em->flush();
	}

}