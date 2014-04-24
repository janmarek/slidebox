<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\Generator\Generator;
use Presidos\Presentation\Presentation;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presentation\ThemeRepository;
use Presidos\Presentation\UploadedImageFileRepository;
use Presidos\Presentation\UploadedImageRepository;
use Presidos\Presenter\BasePresenter;
use Presidos\User\UserRepository;

class EditorPresenter extends BasePresenter
{

	use DeletePresentationTrait;

	/** @var Generator */
	private $generator;

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

	/** @var UploadedImageFileRepository */
	private $uploadedImageFileRepository;

	/** @var UploadedImageRepository */
	private $uploadedImageRepository;

	public function __construct(Generator $generator, PresentationRepository $presentationRepository,
		ThemeRepository $themeRepository, UserRepository $userRepository, EntityManager $em,
		UploadedImageFileRepository $uploadedImageFileRepository, UploadedImageRepository $uploadedImageRepository)
	{
		$this->generator = $generator;
		$this->presentationRepository = $presentationRepository;
		$this->themeRepository = $themeRepository;
		$this->em = $em;
		$this->userRepository = $userRepository;
		$this->uploadedImageFileRepository = $uploadedImageFileRepository;
		$this->uploadedImageRepository = $uploadedImageRepository;
	}

	protected function startup()
	{
		parent::startup();
		$this->checkLoggedIn();

		$this->presentation = $this->presentationRepository->find($this->getParameter('id'));
		$this->checkExistence($this->presentation);

		$user = $this->getUser()->getIdentity();

		if (!$this->presentation->canEditPresentation($user)) {
			$this->error('You cannot edit this presentation', 403);
		}

		if ($this->presentation->isDeleted()) {
			$this->error('Presentation is deleted');
		}

		if ($this->presentation->isLockedForEdit($user)) {
			$this->flashMessage('Presentation is being edited by user ' . $this->presentation->getLastUser()->getName() . '.');
			$this->redirect('List:');
		} else {
			$this->presentation->lockForEdit($user);
			$this->em->flush();
		}

		$this->context->texy->process($this->presentation->getTexy());
	}

	public function renderDefault($id)
	{
		$user = $this->getUser()->getIdentity();
		$themes = $this->themeRepository->findAll();

		$this->template->presentation = $this->presentation;
		$this->template->themes = $themes;
		$this->template->isOwner = $this->presentation->isOwner($user);
	}

	/**
	 * @secured
	 */
	public function handlePreview()
	{
		$texy = $this->getPostParameter('text');

		$presentation = $this->generator->getPresentation($texy);
		$html = $presentation->getHtml();
		$name = $presentation->getName();

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

	/**
	 * @secured
	 */
	public function handleUploadImage()
	{
		$files = $this->getRequest()->getFiles();

		if (!isset($files['image'])) {
			$this->error();
		}

		$result = $this->uploadedImageFileRepository->upload($this->presentation, $files['image']);

		$this->sendJson([
			'errors' => $result->getErrors(),
			'url' => $result->getUrl(),
		]);
	}

	/**
	 * @secured
	 */
	public function handleUploadedImagesList()
	{
		$images = [];

		foreach ($this->uploadedImageRepository->findByPresentation($this->presentation) as $image) {
			$images[] = [
				'name' => $image->getName(),
				'url' => $this->uploadedImageFileRepository->getUrl($image),
			];
		}

		$this->sendJson([
			'images' => $images,
		]);
	}

}
