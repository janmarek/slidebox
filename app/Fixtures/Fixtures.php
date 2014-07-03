<?php

namespace SlideBox\Fixtures;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;

/**
 * @author Jan Marek
 */
class Fixtures
{

	private $purger;

	private $executor;

	private $loader;

	public function __construct(EntityManager $em)
	{
		$this->purger = new ORMPurger();
		$this->executor = new ORMExecutor($em, $this->purger);
		$this->loader = new Loader();
	}

	public function addFixtures()
	{
		$this->loader->loadFromDirectory(__DIR__ . '/Fixtures');
	}

	public function addTestData()
	{
		$this->loader->loadFromDirectory(__DIR__ . '/TestData');
	}

	public function execute()
	{
		$this->executor->execute($this->loader->getFixtures());
	}

} 