<?php

namespace SlideBox\Presentation\Generator;

use DOMDocument;
use DOMElement;
use Nette\Utils\Html;
use Symfony\Component\DomCrawler\Crawler;
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

	public function getSourceCodes()
	{
		$available = [
			'actionscript', 'apache_conf', 'c_cpp', 'clojure', 'coffee', 'csharp', 'css',
			'diff', 'ejs', 'erlang', 'groovy', 'haml', 'haskell', 'html', 'ini',
			'java', 'javascript', 'json', 'jsx', 'latex', 'less', 'lisp', 'lua', 'makefile',
			'objectivec', 'pascal', 'perl', 'php', 'prolog', 'python', 'ruby', 'sass', 'scala',
			'scss', 'sh', 'smarty', 'sql', 'stylus', 'twig', 'typescript', 'xml', 'xquery', 'yaml'
		];

		$aliases = [
			'js' => 'javascript',
			'c' => 'c_cpp',
			'cpp' => 'c_cpp',
		];

		$present = [];

		$xpath = new \DOMXPath($this->document);

		foreach ($available as $lang) {
			if ($xpath->query($this->getSourceCodeXPath($lang))->length > 0) {
				$present[$lang] = TRUE;
			}
		}
		foreach ($aliases as $alias => $lang) {
			if ($xpath->query($this->getSourceCodeXPath($alias))->length > 0) {
				$present[$lang] = TRUE;
			}
		}

		return array_keys($present);
	}

	private function getSourceCodeXPath($lang)
	{
		return "//pre[@class='$lang']/code";
	}

}