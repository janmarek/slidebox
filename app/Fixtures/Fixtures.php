<?php

namespace Presidos\Fixtures;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\Theme;

/**
 * @author Jan Marek
 */
class Fixtures
{

	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function install()
	{
		$defaultTheme = new Theme();
		$defaultTheme->setName('Default');
		$defaultTheme->setClassName('theme-default');
		$defaultTheme->setPublic(TRUE);

		$darkTheme = new Theme();
		$darkTheme->setName('Dark');
		$darkTheme->setClassName('theme-dark');
		$darkTheme->setPublic(TRUE);

		$this->em->persist($defaultTheme);
		$this->em->persist($darkTheme);
		$this->em->flush();
	}

} 