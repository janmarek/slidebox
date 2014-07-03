<?php

namespace SlideBox\Fixtures\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SlideBox\Presentation\Theme;
use SlideBox\Presentation\ThemeVariant;

/**
 * @author Jan Marek
 */
class Themes extends AbstractFixture
{

	public function load(ObjectManager $em)
	{
		$defaultTheme = new Theme('Classic', 'theme-classic');
		$orangeVariant = $defaultTheme->addVariant('Orange', 'variant-classic-orange', 'ff681f', 'chrome');
		$this->addReference('theme-classic-orange', $orangeVariant);
		$defaultTheme->addVariant('Blue', 'variant-classic-blue', '3483D2', 'chrome');
		$defaultTheme->addVariant('Green', 'variant-classic-green', '3b741d', 'chrome');
		$defaultTheme->addVariant('Red', 'variant-classic-red', 'ae0006', 'chrome');
		$defaultTheme->addVariant('Violet', 'variant-classic-violet', '43229e', 'chrome');
		$em->persist($defaultTheme);

		$darkTheme = new Theme('Dark', 'theme-dark');
		$darkTheme->addVariant('Blue', 'variant-dark-blue', '2c576f', 'terminal');
		$darkTheme->addVariant('Green', 'variant-dark-green', '156f40', 'terminal');
		$darkTheme->addVariant('Brown', 'variant-dark-brown', '795729', 'terminal');
		$em->persist($darkTheme);

		$pastelTheme = new Theme('Pastel', 'theme-pastel');
		$pastelTheme->addVariant('Lime', 'variant-pastel-lime', 'c8ff8b', 'chrome');
		$pastelTheme->addVariant('Blue', 'variant-pastel-blue', 'd1e8ff', 'chrome');
		$pastelTheme->addVariant('Banana', 'variant-pastel-banana', 'fff38f', 'chrome');
		$em->persist($pastelTheme);

		$plainTheme = new Theme('Plain', 'theme-plain');
		$plainTheme->addVariant('Blue', 'variant-plain-blue', '2b5288', 'terminal');
		$plainTheme->addVariant('Green', 'variant-plain-green', '397335', 'terminal');
		$plainTheme->addVariant('White', 'variant-plain-white', 'ffffff', 'chrome');
		$plainTheme->addVariant('Black', 'variant-plain-black', '000000', 'terminal');
		$plainTheme->addVariant('Violet', 'variant-plain-violet', '43229e', 'terminal');
		$em->persist($plainTheme);

		$paperTheme = new Theme('Paper', 'theme-paper');
		$paperTheme->addVariant('Beige', 'variant-paper-beige', 'ebdfc9', 'chrome');
		$paperTheme->addVariant('Gray', 'variant-paper-gray', 'bfd1dd', 'chrome');
		$em->persist($paperTheme);

		$em->flush();
	}

}