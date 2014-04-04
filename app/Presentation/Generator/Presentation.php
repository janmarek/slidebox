<?php

namespace Presidos\Presentation\Generator;

use TexyHtml;

/**
 * @author Jan Marek
 */
class Presentation
{

	/** @var TexyHtml[] */
	private $slides = [];

	/** @var bool */
	private $newSlideIsEmpty;

	/** @var TexyHtml */
	private $newSlide;

	/** @var TexyHtml */
	private $newSlideHeader;

	/** @var TexyHtml */
	private $newSlideContent;

	/** @var string|NULL */
	private $name = NULL;
	
	public function __construct()
	{
		$this->newSlide();
	}

	public function getSlides()
	{
		return $this->slides;
	}
	
	public function newSlide()
	{
		if ($this->newSlideIsEmpty === TRUE) {
			return;
		}

		$this->newSlideIsEmpty = TRUE;
		$this->newSlide = TexyHtml::el('div', ['class' => 'slide']);
		$this->newSlideHeader = TexyHtml::el('div', ['class' => 'slide-header']);
		$this->newSlideContent = TexyHtml::el('div', ['class' => 'slide-content']);
		$this->newSlide->add($this->newSlideHeader);
		$this->newSlide->add($this->newSlideContent);
		$this->slides[] = $this->newSlide;
	}

	public function addHeading(TexyHtml $html)
	{
		$this->newSlideIsEmpty = FALSE;
		$this->newSlideHeader->add($html);
		if ($this->name === NULL) {
			$this->name = $html->getText();
		}
	}

	public function addContent(TexyHtml $html)
	{
		$this->newSlideIsEmpty = FALSE;
		$this->newSlideContent->add($html);
	}

	public function getName()
	{
		return $this->name;
	}

}