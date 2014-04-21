<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\Presentation;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PresentPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var GeneratorTexy */
	private $generatorTexy;

	/** @var EntityManager */
	private $em;

	/** @var Presentation */
	private $presentation;

	public function __construct(PresentationRepository $presentationRepository, GeneratorTexy $generatorTexy,
		EntityManager $em)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generatorTexy = $generatorTexy;
		$this->em = $em;
	}

	protected function startup()
	{
		parent::startup();

		$this->presentation = $this->presentationRepository->find($this->getParameter('id'));
		$this->checkExistence($this->presentation);

		$user = $this->getUser()->getIdentity();

		if (!$this->presentation->isPublished() && !$this->presentation->canEditPresentation($user)) {
			$this->error('Unauthorized access to presentation.');
		}

		$this->presentation->increaseVisits($user);

		$this->template->presentation = $this->presentation;
		$this->template->html = $this->generatorTexy->process($this->presentation->getTexy());
	}

	public function renderDefault($id, $edit = FALSE)
	{
		$this->template->isEmbed = FALSE;
		$this->template->edit = $edit;
	}

	public function renderEmbed($id)
	{
		$this->template->isEmbed = TRUE;
		$this->setView('default');
	}

}
