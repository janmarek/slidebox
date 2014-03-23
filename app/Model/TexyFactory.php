<?php

namespace Presidos\Model;

use Texy;

/**
 * @author Jan Marek
 */
class TexyFactory
{

	/**
	 * @return Texy
	 */
	public function createTexy()
	{
		$texy = new Texy();

		$texy->tabWidth = 4;
		$texy->headingModule->balancing = 'fixed';

		return $texy;
	}

} 