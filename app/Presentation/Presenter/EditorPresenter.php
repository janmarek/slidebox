<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presentation\ThemeRepository;
use Presidos\Presenter\BasePresenter;

class EditorPresenter extends BasePresenter
{

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
		$themes = $this->themeRepository->getThemesForUser($user);

		$this->template->presentation = $presentation;
		$this->template->themes = $themes;
	}

	/**
	 * @secured
	 */
	public function handlePreview()
	{
		$request = $this->getHttpRequest();
		$id = $request->getPost('id');
		$texy = $request->getPost('text');

		$html = $this->generatorTexy->process($texy);
		$name = $this->generatorTexy->getLastPresentation()->getName();

		$presentation = $this->presentationRepository->findByUserAndId($this->getUser()->getIdentity(), $id);
		$presentation->setTexy($texy);
		$this->em->flush();

		$this->sendJson([
			'updated' => new \DateTime(),
			'name' => $name,
			'html' => $html,
		]);
	}

	/**
	 * @secured
	 */
	public function handleSaveTheme()
	{
		$request = $this->getHttpRequest();

		$theme = $this->themeRepository->find($request->getPost('theme'));
		$presentation = $this->presentationRepository->findByUserAndId(
			$this->getUser()->getIdentity(),
			$request->getPost('id')
		);

		$presentation->setTheme($theme);
		$this->em->flush();

		$this->sendJson([
			'ok' => TRUE,
		]);
	}

}
