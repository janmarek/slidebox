<?php

namespace Presidos\Test\Presentation\Generator;

use Presidos\Presentation\Generator\Generator;
use Presidos\Test\BaseTestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class GeneratorTest extends BaseTestCase
{

	public function testEmpty()
	{
		$generator = new Generator($this->getContainer()->texy);
		$presentation = $generator->getPresentation('');
		$html = $presentation->getHtml();
		Assert::equal(file_get_contents(__DIR__ . '/empty.html'), $html);
		Assert::null($presentation->getName());
	}

	public function testColumns()
	{
		$generator = new Generator($this->getContainer()->texy);
		$html = $generator->getPresentation(file_get_contents(__DIR__ . '/input.texy'))->getHtml();
		Assert::equal(file_get_contents(__DIR__ . '/output.html'), $html);
	}

	public function testName()
	{
		$generator = new Generator($this->getContainer()->texy);
		$name = $generator->getPresentation(file_get_contents(__DIR__ . '/input.texy'))->getName();
		Assert::same('Presentation title', $name);
	}

}

(new GeneratorTest())->run();