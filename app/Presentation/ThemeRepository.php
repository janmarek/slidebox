<?php

namespace Presidos\Presentation;

use Presidos\Doctrine\Repository;

/**
 * @author Jan Marek
 */
class ThemeRepository extends Repository
{

	/**
	 * @param string $name
	 * @return Theme|NULL
	 */
	public function findByName($name)
	{
		return $this->findOneBy([
			'name' => $name,
		]);
	}

	/**
	 * @return Theme
	 */
	public function getDefaultTheme()
	{
		return $this->findByName('Orange');
	}

}