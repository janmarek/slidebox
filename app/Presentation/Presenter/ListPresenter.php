<?php

namespace SlideBox\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Utils\Html;
use SlideBox\Presentation\PresentationFactory;
use SlideBox\Presentation\PresentationRepository;
use SlideBox\Presenter\BasePresenter;

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

	public function startup()
	{
		parent::startup();
		$this->checkLoggedIn();
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
	public function handleDelete($id)
	{
		$this->checkLoggedIn();
		$presentation = $this->presentationRepository->findByUserAndId($this->getUser()->getIdentity(), $id);
		$this->checkExistence($presentation);

		$presentation->setDeleted(TRUE);
		$this->em->flush();

		$msg = Html::el();
		$msg->add('Presentation "' . $presentation->getName() . '" has been successfully deleted. ');
		$msg->add(Html::el('a', [
			'href' => $this->link('recover!', ['id' => $id]),
		])->setText('Undo'));

		$this->flashMessage($msg);
		$this->redirect('default');
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
