<?php

namespace Presidos\Test;

use Mockista\Registry;
use Nette\Application\UI\Presenter;
use Tester\TestCase;

abstract class BaseTestCase extends TestCase
{

	/** @var Registry */
	protected $mockista;

	public function setUp()
	{
		$this->mockista = new Registry();
	}

	public function tearDown()
	{
		$this->mockista->assertExpectations();
	}

	/**
	 * @return \SystemContainer
	 */
	public function getContainer()
	{
		return $GLOBALS['container'];
	}

	/**
	 * @param string $name
	 * @return Presenter
	 */
	public function getPresenter($name)
	{
		$presenter = $this->getContainer()
			->getByType('Nette\Application\IPresenterFactory')
			->createPresenter($name);

		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

}