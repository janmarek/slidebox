<?php

namespace Presidos\Presentation\Control;

trait PresentationPreviewPresenterFactory
{

	protected function createComponentPresentationPreview()
	{
		return new PresentationPreview();
	}

} 