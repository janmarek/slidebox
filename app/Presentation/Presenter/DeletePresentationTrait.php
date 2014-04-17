<?php

namespace Presidos\Presentation\Presenter;

trait DeletePresentationTrait
{

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

		$this->flashMessage('Presentation has been successfully deleted.');
		$this->redirect('List:trash');
	}

} 