<?php

namespace Presidos\Presentation;

use Presidos\Doctrine\Repository;

/**
 * @author Jan Marek
 */
class ThemeVariantRepository extends Repository
{

	/**
	 * @return ThemeVariant
	 */
	public function getDefault()
	{
		return $this->findOneBy([
			'className' => 'variant-classic-orange',
		]);
	}

}