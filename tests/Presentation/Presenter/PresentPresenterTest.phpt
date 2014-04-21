<?php

namespace Presidos\Test\Presentation\Presenter;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\TextResponse;
use Presidos\Presentation\Presenter\EditorPresenter;
use Presidos\Test\PresenterTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class PresentPresenterTest extends PresenterTestCase
{

	/** @var EditorPresenter */
	private $presenter;

	public function setUp()
	{
		parent::setUp();
		$this->presenter = $this->getPresenter('Presentation:Present');
	}

	private function presentationByName($name)
	{
		return $this->getContainer()->presentationRepository->findOneBy(['name' => $name]);
	}

	public function testNotFound()
	{
		Assert::throws(function () {
			$this->presenter->runGet('default', ['id' => 99999999]);
		}, BadRequestException::class, NULL, 404);
	}

	public function testNotPublished()
	{
		$presentation = $this->presentationByName('Presentation 3');
		Assert::throws(function () use ($presentation) {
			$this->presenter->runGet('default', ['id' => $presentation->getId()]);
		}, BadRequestException::class, NULL, 404);
	}

	public function testIncreaseVisits()
	{
		$this->getContainer()->user->logout();
		$presentation = $this->presentationByName('Presentation 1');

		Assert::equal(0, $presentation->getVisits());

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);

		Assert::type(TextResponse::class, $response);
		Assert::same($presentation, $response->getSource()->presentation);
		Assert::equal(1, $presentation->getVisits());
	}

	public function testNotIncreaseVisitsWhenAuthorIsLoggedIn()
	{
		$presentation = $this->presentationByName('Presentation 1');
		$this->login('Petr');

		Assert::equal(0, $presentation->getVisits());

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);

		Assert::equal(0, $presentation->getVisits());
	}

}

(new PresentPresenterTest())->run();