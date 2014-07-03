<?php

namespace SlideBox\Test\Presentation\Presenter;

use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use SlideBox\Presentation\Presenter\EditorPresenter;
use SlideBox\Test\IntegrationTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class EditorPresenterTest extends IntegrationTestCase
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

		Assert::type(TextResponse::class, $response);
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
		}, BadRequestException::class, NULL, 403);
	}

	public function testEditAsCollaborator()
	{
		$this->login('Petr');
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);
		Assert::type(TextResponse::class, $response);
	}

	public function testEditLocks()
	{
		$this->login('Petr');
		$petr = $this->getContainer()->userRepository->findOneBy(['name' => 'Petr']);
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);
		Assert::same($petr, $presentation->getLastUser());
	}

	public function testEditLocked()
	{
		$this->login('Petr');
		$honza = $this->getContainer()->userRepository->findOneBy(['name' => 'Honza']);
		$presentation = $this->presentationByName('Presentation 1');
		$presentation->lockForEdit($honza);

		$response = $this->presenter->runGet('default', ['id' => $presentation->getId()]);
		Assert::type(RedirectResponse::class, $response);
		Assert::contains('/presentation-list', $response->getUrl());
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
		$themeVariant = $this->getContainer()->themeVariantRepository->findOneBy(['className' => 'variant-dark-blue']);

		$response = $this->presenter->runPost('default', [
			'do' => 'saveTheme',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'themeVariant' => $themeVariant->getId(),
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::same('variant-dark-blue', $presentation->getThemeVariant()->getClassName());
	}

	public function testSaveCollaborators()
	{
		$this->login('Honza');
		$pepa = $this->getContainer()->userRepository->findOneBy(['name' => 'Pepa']);
		$franta = $this->getContainer()->userRepository->findOneBy(['name' => 'Franta']); // not allowed
		$presentation = $this->presentationByName('Presentation 1');

		$response = $this->presenter->runPost('default', [
			'do' => 'saveCollaborators',
			'id' => $presentation->getId(),
			'_sec' => 'csrf',
		], [
			'id' => $presentation->getId(),
			'collaborators' => [$pepa->getId(), $franta->getId()],
		]);

		Assert::type(JsonResponse::class, $response);
		Assert::equal([$pepa], $presentation->getCollaborators());
	}

	public function testSaveCollaboratorsIsNotAllowedForCollaborators()
	{
		$this->login('Petr');
		$pepa = $this->getContainer()->userRepository->findOneBy(['name' => 'Pepa']);
		$presentation = $this->presentationByName('Presentation 1');

		Assert::exception(function () use ($presentation, $pepa) {
			$this->presenter->runPost('default', [
				'do' => 'saveCollaborators',
				'id' => $presentation->getId(),
				'_sec' => 'csrf',
			], [
				'id' => $presentation->getId(),
				'collaborators' => [$pepa->getId()],
			]);
		}, BadRequestException::class, NULL, 403);
	}

}

(new EditorPresenterTest())->run();