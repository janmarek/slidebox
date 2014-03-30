<?php

namespace Presidos\Presentation\Presenter;

use Presidos\Presentation\HtmlGenerator;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PresentPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var HtmlGenerator */
	private $htmlGenerator;

	public function __construct(PresentationRepository $presentationRepository, HtmlGenerator $htmlGenerator)
	{
		$this->presentationRepository = $presentationRepository;
		$this->htmlGenerator = $htmlGenerator;
	}

	public function renderDefault($id)
	{
		$presentation = $this->presentationRepository->find($id);
		$this->checkExistence($presentation);

		if (!$presentation->isPublished() && $this->getUser()->getIdentity() !== $presentation->getUser()) {
			$this->error('Unauthorized access to presentation.', 403);
		}

		$this->template->presentation = $presentation;
		$this->template->html = $this->htmlGenerator->getPresentationHtml($presentation->getTexy())->getHtml();
	}

}
