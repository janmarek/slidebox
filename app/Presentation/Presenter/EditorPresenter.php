<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Utils\Arrays;
use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\Presentation;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presentation\ThemeRepository;
use Presidos\Presenter\BasePresenter;
use Presidos\User\UserRepository;

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

	/** @var UserRepository */
	private $userRepository;

	/** @var Presentation */
	private $presentation;

	public function __construct(GeneratorTexy $generatorTexy, PresentationRepository $presentationRepository,
		ThemeRepository $themeRepository, UserRepository $userRepository, EntityManager $em)
	{
		$this->generatorTexy = $generatorTexy;
		$this->presentationRepository = $presentationRepository;
		$this->themeRepository = $themeRepository;
		$this->em = $em;
		$this->userRepository = $userRepository;
	}

	protected function startup()
	{
		parent::startup();
		$this->checkLoggedIn();

		$presentation = $this->presentationRepository->find($this->getParameter('id'));
		$this->checkExistence($presentation);

		if ($presentation->isDeleted()) {
			$this->error('Presentation is deleted');
		}

		if ($presentation->canEditPresentation($this->getUser()->getIdentity())) {
			$this->presentation = $presentation;
		} else {
			$this->error('You cannot edit this presentation', 403);
		}
	}

	public function renderDefault($id)
	{
		$user = $this->getUser()->getIdentity();
		$themes = $this->themeRepository->getThemesForUser($user);

		$this->template->presentation = $this->presentation;
		$this->template->themes = $themes;
	}

	/**
	 * @secured
	 */
	public function handlePreview()
	{
		$texy = $this->getPostParameter('text');

		$html = $this->generatorTexy->process($texy);
		$name = $this->generatorTexy->getLastPresentation()->getName();

		if (!$this->presentation->isNameLocked()) {
			$this->presentation->setName($name);
		}
		$this->presentation->setTexy($texy);
		$this->em->flush();

		$this->sendJson([
			'name' => $name,
			'html' => $html,
			'updated' => $this->presentation->getUpdated()->format('c'),
		]);
	}

	/**
	 * @secured
	 */
	public function handleSaveTheme()
	{
		$theme = $this->themeRepository->find($this->getPostParameter('theme'));

		$this->presentation->setTheme($theme);
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
		if (!$this->presentation->isNameLocked() && $this->getPostParameter('name') !== $this->presentation->getName()) {
			$this->presentation->lockName();
		}

		$this->presentation->setName($this->getPostParameter('name'));
		$this->presentation->setDescription($this->getPostParameter('description'));
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
			'presentation' => $this->presentation,
		]);
	}

	/**
	 * @secured
	 */
	public function handlePublish()
	{
		$this->presentation->publish();
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
		]);
	}

	/**
	 * @secured
	 */
	public function handleSaveCollaborators()
	{
		if (!$this->presentation->isOwner($this->getUser()->getIdentity())) {
			$this->error('User is not owner.', 403);
		}

		$collaboratorIds = $this->getPostParameter('collaborators') ?: [];
		$collaborators = $this->userRepository->findAllowedByIds($collaboratorIds);
		$this->presentation->setCollaborators($collaborators);
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
		]);
	}

}
