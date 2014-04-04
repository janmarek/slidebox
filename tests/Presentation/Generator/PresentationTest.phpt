<?php

namespace Presidos\Test\Presentation\Generator;

use Presidos\Presentation\Generator\Presentation;
use Presidos\Test\BaseTestCase;
use Tester\Assert;
use TexyHtml;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class PresentationTest extends BaseTestCase
{

	public function testEmpty()
	{
		$presentation = new Presentation();
		$slides = $presentation->getSlides();
		Assert::equal(1, count($slides));

		Assert::equal('div', $slides[0]->getName());
		Assert::equal('slide', $slides[0]->attrs['class']);
		Assert::equal('div', $slides[0]->getChildren()[0]->getName());
		Assert::equal('slide-header', $slides[0]->getChildren()[0]->attrs['class']);
		Assert::equal('div', $slides[0]->getChildren()[1]->getName());
		Assert::equal('slide-content', $slides[0]->getChildren()[1]->attrs['class']);

		Assert::null($presentation->getName());
	}

	public function testSkipEmptySlide()
	{
		$presentation = new Presentation();
		$presentation->newSlide();
		$presentation->newSlide();
		$presentation->addContent(TexyHtml::el('p')->setText('lorem ipsum'));
		$presentation->newSlide();

		Assert::equal(2, count($presentation->getSlides()));
	}

	public function testMainHeading()
	{
		$presentation = new Presentation();
		$presentation->addHeading(TexyHtml::el('h2')->setText('lorem'));
		$presentation->addContent(TexyHtml::el('p')->setText('ipsum'));
		$presentation->newSlide();
		$presentation->addHeading(TexyHtml::el('h2')->setText('amet'));

		Assert::equal('lorem', $presentation->getName());
	}

}

(new PresentationTest())->run();