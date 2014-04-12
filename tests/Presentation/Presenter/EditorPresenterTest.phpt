<?php

namespace Presidos\Test\Presentation\Presenter;

use Nette\Application\Request;
use Nette\Security\IIdentity;
use Presidos\Test\BaseTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class EditorPresenterTest extends BaseTestCase
{

	public function testNotFound()
	{
		$user = $this->getContainer()->userRepository->find(1);

		var_dump($user);

		$this->getContainer()->user->login($user);

		$presenter = $this->getPresenter('Presentation:Editor');

		$response = $presenter->run(new Request('Presentation:Editor', 'GET', [
			'action' => 'default',
			'id' => 99999999
		]));

		var_dump($response);

		Assert::true(1);
	}

}

//(new EditorPresenterTest())->run();