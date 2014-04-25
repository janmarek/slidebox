<?php

namespace Presidos\Presentation\Generator;

use Symfony\Component\DomCrawler\Crawler;
use Texy;

/**
 * @author Jan Marek
 */
class Generator
{

	/**
	 * @param string $html
	 * @return Presentation
	 */
	public function getPresentation($html)
	{
		$presentation = new Presentation();

		$dom = new Crawler();
		$dom->addHtmlContent($html);

		foreach ($dom->filter('body > *') as $child) {
			if ($child->nodeName === 'h1' || $child->nodeName === 'h2') {
				$presentation->newSlide();
				$presentation->addHeading($child);
				$this->slideClass($presentation, $child);
			} elseif ($child->nodeName === 'hr') {
				$presentation->newSlide();
				$this->slideClass($presentation, $child);
			} elseif ($child->nodeName === 'div' && $this->hasClass($child, 'figure-left')) {
				$presentation->addLeftContent($child);
			} elseif ($child->nodeName === 'div' && $this->hasClass($child, 'figure-right')) {
				$presentation->addRightContent($child);
			} elseif ($this->firstAndOnlyChildIsImage($child) && $this->hasClass($child->firstChild, 'image-left')) {
				$presentation->addLeftContent($child);
			} elseif ($this->firstAndOnlyChildIsImage($child) && $this->hasClass($child->firstChild, 'image-right')) {
				$presentation->addRightContent($child);
			} else {
				$presentation->addContent($child);
			}
		}

		return $presentation;
	}

	private function slideClass(Presentation $presentation, $child)
	{
		$allowedSlideClasses = ['main', 'last'];
		foreach ($allowedSlideClasses as $class) {
			if ($this->hasClass($child, $class)) {
				$presentation->addSlideClass($class);
			}
		}
	}

	private function firstAndOnlyChildIsImage($el)
	{
		return $el->nodeName === 'div' && $el->childNodes->length === 1 && $el->firstChild->nodeName === 'img';
	}

	public function hasClass($el, $class)
	{
		return in_array($class, explode(' ', $el->getAttribute('class')));
	}

} 