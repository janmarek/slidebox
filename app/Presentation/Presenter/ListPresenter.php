<?php

namespace Presidos\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Presidos\Presentation\PresentationFactory;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class ListPresenter extends BasePresenter
{

	use DeletePresentationTrait;

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

	public function renderTrash()
	{
		$presentations = $this->presentationRepository->findDeletedByUser($this->getUser()->getIdentity());
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

	/**
	 * @secured
	 */
	public function handleRecover($id)
	{
		$presentation = $this->presentationRepository->findByUserAndId($this->getUser()->getIdentity(), $id, TRUE);
		$this->checkExistence($presentation);

		$presentation->setDeleted(FALSE);
		$this->em->flush();

		$this->flashMessage('Presentation has been successfully recovered.');
		$this->redirect('default');
	}

}
