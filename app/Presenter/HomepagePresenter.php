<?php

namespace SlideBox\Presenter;

use SlideBox\Presentation\Control\PresentationPreviewPresenterFactory;
use SlideBox\Presentation\PresentationRepository;

class HomepagePresenter extends BasePresenter
{

	use PresentationPreviewPresenterFactory;

	/** @var PresentationRepository */
	private $presentationRepository;

	public function __construct(PresentationRepository $presentationRepository)
	{
		$this->presentationRepository = $presentationRepository;
	}

	public function renderDefault()
	{
		$this->template->newestPresentations = $this->presentationRepository->findNewestPublished(4);
		$this->template->mostVisitedPresentations = $this->presentationRepository->findPublishedByVisits(4);
	}

}
