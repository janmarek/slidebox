<?php

namespace Presidos\Presentation\Generator;

use Texy;
use TexyHtml;

/**
 * @author Jan Marek
 */
class SlidesTexyHandler
{

	private $presentation;

	public function __invoke(Texy $texy, TexyHtml $dom)
	{
		$this->presentation = new Presentation();

		foreach ($dom->getChildren() as $child) {
			if ($child->getName() === 'h1' || $child->getName() === 'h2') {
				$this->presentation->newSlide();
				$this->presentation->addHeading($child);
			} elseif ($child->getName() === 'hr') {
				$this->presentation->newSlide();
			} else {
				$this->presentation->addContent($child);
			}
		}

		// set new content
		$dom->removeChildren();
		foreach ($this->presentation->getSlides() as $slide) {
			$dom->add($slide);
		}
	}

	/**
	 * @return Presentation
	 */
	public function getPresentation()
	{
		return $this->presentation;
	}

} 