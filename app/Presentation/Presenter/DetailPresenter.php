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

		if (!$presentation->isPublished() && $this->getUser()->getIdentity() !== $presentation->getUser()) {
			$this->error('Unauthorized access to presentation.', 403);
		}

		$this->template->presentation = $presentation;
	}

}
