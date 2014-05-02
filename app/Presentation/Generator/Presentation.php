<?php

namespace Presidos\Presentation\Generator;

use DOMDocument;
use DOMElement;
use Nette\Utils\Html;
use TexyHtml;

/**
 * @author Jan Marek
 */
class Presentation
{

	/** @var DOMDocument */
	private $document;

	/** @var bool */
	private $newSlideIsEmpty;

	/** @var DOMElement */
	private $newSlide;

	/** @var DOMElement */
	private $newSlideHeader;

	/** @var DOMElement */
	private $newSlideContent;

	/** @var DOMElement|NULL */
	private $newSlideLeftContent;

	/** @var DOMElement|NULL */
	private $newSlideRightContent;

	/** @var string|NULL */
	private $name = NULL;
	
	public function __construct()
	{
		$this->document = new DOMDocument('1.0', 'UTF-8');
		$this->newSlide();
	}

	public function getHtml()
	{
		return $this->document->saveHTML();
	}
	
	public function newSlide()
	{
		if ($this->newSlideIsEmpty === TRUE) {
			return;
		}

		$this->newSlideIsEmpty = TRUE;
		$this->newSlide = $this->el('div', ['class' => 'slide']);
		$this->newSlideHeader = $this->el('div', ['class' => 'slide-header']);
		$this->newSlideContent = $this->el('div', ['class' => 'slide-content']);
		$this->newSlideLeftContent = NULL;
		$this->newSlideRightContent = NULL;
		$this->newSlide->appendChild($this->newSlideHeader);
		$this->newSlide->appendChild($this->newSlideContent);
		$this->document->appendChild($this->newSlide);
	}

	public function addHeading(DOMElement $el)
	{
		$this->newSlideIsEmpty = FALSE;
		$this->newSlideHeader->appendChild($this->import($el));
		if ($this->name === NULL) {
			$this->name = $el->textContent;
		}
	}

	public function addContent($el)
	{
		$this->newSlideIsEmpty = FALSE;
		$this->newSlideContent->appendChild($this->import($el));
	}

	public function getName()
	{
		return $this->name;
	}
	
	private function initColumns()
	{
		if (empty($this->newSlideLeftContent)) {
			$this->newSlideLeftContent = $this->el('div', ['class' => 'left-column']);
			$this->newSlideRightContent = $this->el('div', ['class' => 'right-column']);
			$this->newSlideContent->appendChild($this->newSlideLeftContent);
			$this->newSlideContent->appendChild($this->newSlideRightContent);
		}
	}

	public function addLeftContent($el)
	{
		$this->initColumns();
		$this->newSlideLeftContent->appendChild($this->import($el));
		$this->newSlideContent = $this->newSlideRightContent;
	}

	public function addRightContent($el)
	{
		$this->initColumns();
		$this->newSlideRightContent->appendChild($this->import($el));
		$this->newSlideContent = $this->newSlideLeftContent;
	}

	private function el($name, $attrs = [])
	{
		$el = $this->document->createElement($name);
		foreach ($attrs as $key => $value) {
			$el->setAttribute($key, $value);
		}

		return $el;
	}

	private function import($el)
	{
		return $this->document->importNode($el, TRUE);
	}

	public function addSlideClass($class)
	{
		$this->newSlide->setAttribute('class', $this->newSlide->getAttribute('class') . ' ' . $class);
	}

}