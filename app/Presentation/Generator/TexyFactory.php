<?php

namespace Presidos\Presentation\Generator;

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
		\TexyConfigurator::safeMode($texy);

		$texy->tabWidth = 4;
		$texy->headingModule->balancing = 'fixed';

		$texy->imageModule->leftClass = 'image-left';
		$texy->imageModule->rightClass = 'image-right';
		$texy->figureModule->leftClass = 'figure-left';
		$texy->figureModule->rightClass = 'figure-right';

		$texy->allowedClasses = Texy::ALL;
		$texy->allowed['image'] = TRUE;
		$texy->linkModule->forceNoFollow = FALSE;

		return $texy;
	}

} 