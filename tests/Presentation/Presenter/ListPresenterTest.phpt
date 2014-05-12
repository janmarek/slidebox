<?php

namespace Presidos\Test\Presentation\Presenter;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Presidos\Presentation\Presenter\EditorPresenter;
use Presidos\Test\IntegrationTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class ListPresenterTest extends IntegrationTestCase
{

	/** @var EditorPresenter */
	private $presenter;

	public function setUp()
	{
		parent::setUp();
		$this->presenter = $this->getPresenter('Presentation:List');
	}

	private function presentationByName($name)
	{
		return $this->getContainer()->presentationRepository->findOneBy(['name' => $name]);
	}

	public function testDelete()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runGet('default', [
			'do' => 'delete',
			'_sec' => 'csrf',
			'id' => $presentation->getId(),
		]);

		Assert::type(RedirectResponse::class, $response);
		Assert::true($presentation->isDeleted());
	}

}

(new ListPresenterTest())->run();