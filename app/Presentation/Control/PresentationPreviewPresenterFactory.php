<?php

namespace SlideBox\Presentation\Control;

trait PresentationPreviewPresenterFactory
{

	protected function createComponentPresentationPreview()
	{
		return new PresentationPreview();
	}

} 