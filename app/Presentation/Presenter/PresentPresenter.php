<?php

namespace Presidos\Presentation\Presenter;

use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PresentPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var GeneratorTexy */
	private $generatorTexy;

	public function __construct(PresentationRepository $presentationRepository, GeneratorTexy $generatorTexy)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generatorTexy = $generatorTexy;
	}

	public function renderDefault($id)
	{
		$presentation = $this->presentationRepository->find($id);
		$this->checkExistence($presentation);

		if (!$presentation->isPublished() && $this->getUser()->getIdentity() !== $presentation->getUser()) {
			$this->error('Unauthorized access to presentation.', 403);
		}

		$this->template->presentation = $presentation;
		$this->template->html = $this->generatorTexy->process($presentation->getTexy());
	}

}
