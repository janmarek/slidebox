<?php

namespace SlideBox\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use SlideBox\Presentation\Generator\Generator;
use SlideBox\Presentation\Generator\TexyFactory;
use SlideBox\Presentation\Presentation;
use SlideBox\Presentation\PresentationRepository;
use SlideBox\Presenter\BasePresenter;

class PresentPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var Generator */
	private $generator;

	/** @var EntityManager */
	private $em;

	/** @var Presentation */
	private $presentation;

	/** @var TexyFactory */
	private $texyFactory;

	public function __construct(PresentationRepository $presentationRepository, Generator $generator,
		EntityManager $em, TexyFactory $texyFactory)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generator = $generator;
		$this->em = $em;
		$this->texyFactory = $texyFactory;
	}

	protected function startup()
	{
		parent::startup();

		$this->presentation = $this->presentationRepository->findNotDeleted($this->getParameter('id'));
		$this->checkExistence($this->presentation);

		$user = $this->getUser()->getIdentity();

		if (!$this->presentation->isPublished() && !$this->presentation->isEditableBy($user)) {
			$this->error('Unauthorized access to presentation.');
		}

		$this->presentation->increaseVisits($user);
		$this->em->flush();

		$this->template->presentation = $this->presentation;
		$texy = $this->presentation->getTexy();
		$html = $this->texyFactory->createTexy($this->presentation->getUser())->process($texy);
		$generatorPresentation = $this->generator->getPresentation($html);
		$this->template->html = $generatorPresentation->getHtml();
		$this->template->sourceCodes = $generatorPresentation->getSourceCodes();
		$this->template->ogType = 'presentation';
	}

	public function renderDefault($id, $edit = FALSE)
	{
		$this->template->isEmbed = FALSE;
		$this->template->edit = $edit;
		$this->template->exitLink = $edit ? $this->link('Editor:', ['id' => $id]) : $this->link('Detail:', ['id' => $id]);
	}

	public function renderEmbed($id)
	{
		$this->template->isEmbed = TRUE;
		$this->template->exitLink = NULL;
		$this->setView('default');
	}

}
