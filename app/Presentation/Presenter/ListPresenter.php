<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\Presentation;
use Presidos\Presentation\PresentationFactory;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class ListPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var PresentationFactory */
	private $presentationFactory;

	/** @var EntityManager */
	private $em;

	public function __construct(PresentationRepository $presentationRepository, PresentationFactory $presentationFactory,
		EntityManager $em)
	{
		$this->presentationRepository = $presentationRepository;
		$this->presentationFactory = $presentationFactory;
		$this->em = $em;
	}

	public function renderDefault()
	{
		$presentations = $this->presentationRepository->findByUser($this->getUser()->getIdentity());
		$this->template->presentations = $presentations;
	}

	public function actionCreate()
	{
		$presentation = $this->presentationFactory->create($this->getUser()->getIdentity());
		$this->em->persist($presentation);
		$this->em->flush();

		$this->redirect('Editor:', [
			'id' => $presentation->getId(),
		]);
	}

}
