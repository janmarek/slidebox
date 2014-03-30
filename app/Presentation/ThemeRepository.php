<?php

namespace Presidos\Presentation;

use Presidos\Doctrine\Repository;
use Presidos\User\User;

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
		return $this->findByName('Default');
	}

	/**
	 * @param User $user
	 * @return Theme[]
	 */
	public function getThemesForUser(User $user)
	{
		$qb = $this->createQueryBuilder('t');
		$qb->andWhere('t.public = true or t.user = :user');
		$qb->setParameter('user', $user);

		return $qb->getQuery()->getResult();
	}

}