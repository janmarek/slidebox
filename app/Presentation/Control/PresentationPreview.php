<?php

namespace SlideBox\Presentation\Control;

use Nette\Application\UI\Control;
use SlideBox\Presentation\Presentation;

/**
 * @author Jan Marek
 */
class PresentationPreview extends Control
{

	public function render(Presentation $presentation)
	{
		$template = $this->getPresenter()->createTemplate()->setFile(__DIR__ . '/PresentationPreview.latte');
		$template->presentation = $presentation;
		$template->render();
	}

} 