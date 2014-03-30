<?php

namespace Presidos\Presentation;

use Presidos\Doctrine\Repository;
use Presidos\User\User;

/**
 * @author Jan Marek
 */
class PresentationRepository extends Repository
{

	/**
	 * @param User $user
	 * @param $id
	 * @return Presentation|NULL
	 */
	public function findByUserAndId(User $user, $id)
	{
		return $this->findOneBy([
			'user' => $user,
			'id' => $id,
		]);
	}

	/**
	 * @param User $user
	 * @return Presentation[]
	 */
	public function findByUser(User $user)
	{
		$qb = $this->createQueryBuilder('p');
		$qb->andWhere('p.user = :user')->setParameter('user', $user);
		$qb->orderBy('p.created', 'desc');

		return $qb->getQuery()->getResult();
	}

}