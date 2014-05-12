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

	/** @var Generator */
	private $generator;

	private $html;

	public function setUp()
	{
		parent::setUp();
		$this->html = $this->getContainer()->texy->process(file_get_contents(__DIR__ . '/input.texy'));
		$this->generator = new Generator();
	}

	public function testEmpty()
	{
		$presentation = $this->generator->getPresentation('');
		$html = $presentation->getHtml();
		Assert::equal(file_get_contents(__DIR__ . '/empty.html'), $html);
		Assert::null($presentation->getName());
	}

	public function testColumns()
	{
		$html = $this->generator->getPresentation($this->html)->getHtml();
		Assert::equal(file_get_contents(__DIR__ . '/output.html'), $html);
	}

	public function testName()
	{
		$name = $this->generator->getPresentation($this->html)->getName();
		Assert::same('Presentation title', $name);
	}

}

(new GeneratorTest())->run();