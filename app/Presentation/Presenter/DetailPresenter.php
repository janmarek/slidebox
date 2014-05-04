<?php

namespace Presidos\Presentation\Presenter;

use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class DetailPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	public function __construct(PresentationRepository $presentationRepository)
	{
		$this->presentationRepository = $presentationRepository;
	}

	public function renderDefault($id)
	{
		$presentation = $this->presentationRepository->find($id);
		$this->checkExistence($presentation);

		$this->template->presentationVisible = $presentation->isPublished() || $presentation->isEditableBy($this->getUser()->getIdentity());
		$this->template->presentation = $presentation;
		$this->template->ogType = 'presentation';
	}

}
