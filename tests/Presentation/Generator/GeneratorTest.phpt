<?php

namespace SlideBox\Test\Presentation\Generator;

use SlideBox\Presentation\Generator\Generator;
use SlideBox\Test\BaseTestCase;
use SlideBox\User\User;
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

	private $texy;

	public function setUp()
	{
		parent::setUp();
		$userMock = $this->mockista->create(User::class, ['getId' => 123]);
		$this->texy = $this->getContainer()->texyFactory->createTexy($userMock);
		$this->html = $this->texy->process(file_get_contents(__DIR__ . '/input.texy'));
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