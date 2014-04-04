<?php

namespace Presidos\Presentation\Generator;

/**
 * @author Jan Marek
 */
class TexyFactory
{

	/**
	 * @return GeneratorTexy
	 */
	public function createTexy()
	{
		$texy = new GeneratorTexy(new SlidesTexyHandler());

		$texy->tabWidth = 4;
		$texy->headingModule->balancing = 'fixed';

		return $texy;
	}

} 