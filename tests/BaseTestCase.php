<?php

namespace Presidos\Test;

use Tester\TestCase;

abstract class BaseTestCase extends TestCase
{

	/** @var \Mockista\Registry */
	protected $mockista;

	public function setUp()
	{
		$this->mockista = new \Mockista\Registry();
	}

	public function tearDown()
	{
		$this->mockista->assertExpectations();
	}

}