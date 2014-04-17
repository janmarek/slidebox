<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Utils\Arrays;
use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presentation\ThemeRepository;
use Presidos\Presenter\BasePresenter;

class EditorPresenter extends BasePresenter
{

	use DeletePresentationTrait;

	/** @var GeneratorTexy */
	private $generatorTexy;

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var EntityManager */
	private $em;

	/** @var ThemeRepository */
	private $themeRepository;

	public function __construct(GeneratorTexy $generatorTexy, PresentationRepository $presentationRepository,
		ThemeRepository $themeRepository, EntityManager $em)
	{
		$this->generatorTexy = $generatorTexy;
		$this->presentationRepository = $presentationRepository;
		$this->themeRepository = $themeRepository;
		$this->em = $em;
	}

	protected function startup()
	{
		parent::startup();
		$this->checkLoggedIn();
	}

	public function renderDefault($id)
	{
		$user = $this->getUser()->getIdentity();
		$presentation = $this->presentationRepository->findByUserAndId($user, $id);
		$this->checkExistence($presentation);
		$themes = $this->themeRepository->getThemesForUser($user);

		$this->template->presentation = $presentation;
		$this->template->themes = $themes;
	}

	/**
	 * @secured
	 */
	public function handlePreview()
	{
		$id = $this->getPostParameter('id');
		$texy = $this->getPostParameter('text');

		$html = $this->generatorTexy->process($texy);
		$name = $this->generatorTexy->getLastPresentation()->getName();

		$presentation = $this->presentationRepository->findByUserAndId($this->getUser()->getIdentity(), $id);
		if (!$presentation->isNameLocked()) {
			$presentation->setName($name);
		}
		$presentation->setTexy($texy);
		$this->em->flush();

		$this->sendJson([
			'name' => $name,
			'html' => $html,
			'updated' => $presentation->getUpdated()->format('c'),
		]);
	}

	/**
	 * @secured
	 */
	public function handleSaveTheme()
	{
		$theme = $this->themeRepository->find($this->getPostParameter('theme'));
		$presentation = $this->presentationRepository->findByUserAndId(
			$this->getUser()->getIdentity(),
			$this->getPostParameter('id')
		);

		$presentation->setTheme($theme);
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
		]);
	}

	/**
	 * @secured
	 */
	public function handleSaveDetails()
	{
		$presentation = $this->presentationRepository->findByUserAndId(
			$this->getUser()->getIdentity(),
			$this->getPostParameter('id')
		);

		if (!$presentation->isNameLocked() && $this->getPostParameter('name') !== $presentation->getName()) {
			$presentation->lockName();
		}

		$presentation->setName($this->getPostParameter('name'));
		$presentation->setDescription($this->getPostParameter('description'));
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
			'presentation' => $presentation,
		]);
	}

	/**
	 * @secured
	 */
	public function handlePublish()
	{
		$presentation = $this->presentationRepository->findByUserAndId(
			$this->getUser()->getIdentity(),
			$this->getPostParameter('id')
		);

		$presentation->publish();
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
		]);
	}

}
