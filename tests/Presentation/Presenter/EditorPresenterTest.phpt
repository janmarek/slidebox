<?php

namespace Presidos\Test\Presentation\Presenter;

use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\RedirectResponse;
use Presidos\Presentation\Presenter\EditorPresenter;
use Presidos\Test\PresenterTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class EditorPresenterTest extends PresenterTestCase
{

	/** @var EditorPresenter */
	private $presenter;

	public function setUp()
	{
		parent::setUp();
		$this->presenter = $this->getPresenter('Presentation:Editor');
	}

	private function presentationByName($name)
	{
		return $this->getContainer()->presentationRepository->findOneBy(['name' => $name]);
	}

	public function testNotFound()
	{
		$this->login('Honza');
		Assert::throws(function () {
			$this->presenter->runGet('default', ['id' => 99999999]);
		}, BadRequestException::class, NULL, 404);
	}

	public function testEdit()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);

		// presentation is sent to template
		Assert::same($presentation, $response->getSource()->presentation);
	}

	public function testEditDeleted()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 2');

		Assert::throws(function () use ($presentation) {
			$this->presenter->runGet('default', ['id' => $presentation->getId()]);
		}, BadRequestException::class, NULL, 404);
	}

	public function testEditSomeoneElses()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 3');

		Assert::throws(function () use ($presentation) {
			$this->presenter->runGet('default', ['id' => $presentation->getId()]);
		}, BadRequestException::class, NULL, 404);
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

	public function testEditDetails()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runPost('default', [
			'do' => 'saveDetails',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'name' => 'new name',
			'description' => 'new description',
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::true($presentation->isNameLocked());
		Assert::same('new name', $presentation->getName());
		Assert::same('new description', $presentation->getDescription());
	}

	public function testEditDetailsDontChangeName()
	{
		$this->login('Honza');
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runPost('default', [
			'do' => 'saveDetails',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'name' => 'Presentation 1',
			'description' => 'new description',
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::false($presentation->isNameLocked());
	}

	public function testPublish()
	{
		$this->login('Pepa');
		$presentation = $this->presentationByName('Presentation 3');

		$response = $this->presenter->runPost('default', [
			'do' => 'publish',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'name' => 'Presentation 1',
			'description' => 'new description',
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::false($presentation->isNameLocked());
	}

	public function testSaveTheme()
	{
		$this->login('Pepa');
		$presentation = $this->presentationByName('Presentation 3');
		$theme = $this->getContainer()->themeRepository->findOneBy(['name' => 'Dark']);

		$response = $this->presenter->runPost('default', [
			'do' => 'saveTheme',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'theme' => $theme->getId(),
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::same('Dark', $presentation->getTheme()->getName());
	}

}

(new EditorPresenterTest())->run();