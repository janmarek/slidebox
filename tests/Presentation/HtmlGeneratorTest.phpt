<?php

namespace Presidos\Test\Presentation;

use Presidos\Presentation\HtmlGenerator;
use Presidos\Test\BaseTestCase;
use Tester\Assert;
use Tester\DomQuery;

require __DIR__ . '/../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class HtmlGeneratorTest extends BaseTestCase
{

	public function testGetPresentationHtml()
	{
		$texy = $this->mockista->create('Texy', [
			'process' => file_get_contents(__DIR__ . '/fixtures/htmlGenerator1.html'),
		]);

		$htmlGenerator = new HtmlGenerator($texy);

		$result = $htmlGenerator->getPresentationHtml('...');

		$doc = DomQuery::fromHtml($result->getHtml());
		$slides = $doc->find('div.slide');
		$headings = $doc->find('div.slide h2');
		$lists = $doc->find('div.slide > div.slide-content > ul');
		$paragraphs = $doc->find('div.slide > div.slide-content > p');

		Assert::equal(2, count($slides));
		Assert::equal(2, count($headings));
		Assert::equal(1, count($lists));
		Assert::equal(1, count($paragraphs));
		Assert::true($doc->has('ul li')); // content is copied

		Assert::equal('Heading', $result->getName());
	}

	public function testEmptyHtml()
	{
		$texy = $this->mockista->create('Texy', [
			'process' => '',
		]);

		$htmlGenerator = new HtmlGenerator($texy);

		$result = $htmlGenerator->getPresentationHtml('...');

		$doc = DomQuery::fromHtml($result->getHtml());
		$slides = $doc->find('div.slide');

		Assert::equal(1, count($slides));
		Assert::null($result->getName());
	}

}

(new HtmlGeneratorTest())->run();