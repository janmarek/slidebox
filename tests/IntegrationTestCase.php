<?php

namespace SlideBox\Test;

use Nette\Application\UI\Presenter;
use Tester\Environment;

abstract class IntegrationTestCase extends BaseTestCase
{

	public function setUp()
	{
		parent::setUp();
		Environment::lock('db', __DIR__ . '/../temp');
		$fixtures = $this->getContainer()->fixtures;
		$fixtures->addFixtures();
		$fixtures->addTestData();
		$fixtures->execute();
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
		$presenter->testMode = TRUE;
		$presenter->testPresenterName = $name;

		return $presenter;
	}

	protected function login($name)
	{
		$user = $this->getContainer()->userRepository->findOneBy(['name' => $name]);
		$this->getContainer()->user->login($user);
	}

}