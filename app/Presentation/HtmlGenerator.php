<?php

namespace Presidos\Presentation;

use Symfony\Component\DomCrawler\Crawler;
use Texy;

/**
 * @author Jan Marek
 */
class HtmlGenerator
{

	/**
	 * @var Texy
	 */
	private $texy;

	public function __construct(Texy $texy)
	{
		$this->texy = $texy;
	}

	public function getPresentationHtml($texy)
	{
		$html = $this->texy->process($texy);

		$firstHeading = NULL;

		$original = new Crawler('<?xml version="1.0" encoding="UTF-8"?><body>' . $html . '</body>');
		$new = new \DOMDocument('1.0', 'UTF-8');

		$newSlide = $this->createSlideElement($new);
		$newContent = $this->createSlideContentElement($new, $newSlide);
		$firstElement = TRUE;

		foreach ($original->filter('body > *') as $child) {
			if ($child->nodeName === 'h2' || $child->nodeName === 'h1') {
				if (!$firstElement) {
					$new->appendChild($newSlide);
					$newSlide = $this->createSlideElement($new);
					$newContent = $this->createSlideContentElement($new, $newSlide);
				}

				if (!$firstHeading) {
					$firstHeading = $child->textContent;
				}

				$newSlide->insertBefore($new->importNode($child, TRUE), $newContent);
			} else {
				$newContent->appendChild($new->importNode($child, TRUE));
			}
			$firstElement = FALSE;
		}

		$new->appendChild($newSlide);

		return new HtmlGeneratorResult($new->saveHTML(), $firstHeading);
	}

	private function createSlideElement(\DOMDocument $dom)
	{
		$slide = $dom->createElement('div');
		$slide->setAttribute('class', 'slide');

		return $slide;
	}

	private function createSlideContentElement(\DOMDocument $dom, \DOMElement $slide)
	{
		$content = $dom->createElement('div');
		$content->setAttribute('class', 'slide-content');
		$slide->appendChild($content);

		return $content;
	}

} 