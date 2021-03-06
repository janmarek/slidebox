<?php

namespace SlideBox\Presentation;

use SlideBox\Doctrine\Repository;
use SlideBox\User\User;

/**
 * @author Jan Marek
 */
class PresentationRepository extends Repository
{

	/**
	 * @param User $user
	 * @param int $id
	 * @param bool $includeDeleted
	 * @return Presentation|NULL
	 */
	public function findByUserAndId(User $user, $id, $includeDeleted = FALSE)
	{
		$params = [
			'user' => $user,
			'id' => $id,
		];

		if (!$includeDeleted) {
			$params['deleted'] = FALSE;
		}

		return $this->findOneBy($params);
	}

	/**
	 * @param User $user
	 * @return Presentation[]
	 */
	public function findByUser(User $user)
	{
		$qb = $this->createQueryBuilder('p');
		$qb->leftJoin('p.collaborators', 'c');
		$qb->andWhere('p.user = :user or c.id = :user')->setParameter('user', $user->getId());

		$qb->andWhere('p.deleted = FALSE');
		$qb->orderBy('p.updated', 'desc');

		return $qb->getQuery()->getResult();
	}

	/**
	 * @param User $user
	 * @return Presentation[]
	 */
	public function findDeletedByUser(User $user)
	{
		$qb = $this->createQueryBuilder('p');
		$qb->andWhere('p.user = :user')->setParameter('user', $user);
		$qb->andWhere('p.deleted = TRUE');
		$qb->orderBy('p.updated', 'desc');

		return $qb->getQuery()->getResult();
	}

	/**
	 * @param User $user
	 * @return Presentation[]
	 */
	public function findPublishedByUser(User $user)
	{
		$qb = $this->createPublishedQb();
		$qb->andWhere('p.user = :user')->setParameter('user', $user);
		$qb->orderBy('p.updated', 'desc');

		return $qb->getQuery()->getResult();
	}

	public function findPublishedByVisits($limit)
	{
		$qb = $this->createPublishedQb();
		$qb->orderBy('p.visits', 'desc');
		$qb->setMaxResults($limit);

		return $qb->getQuery()->getResult();
	}

	public function findNewestPublished($limit)
	{
		$qb = $this->createPublishedQb();
		$qb->orderBy('p.publishedAt', 'desc');
		$qb->setMaxResults($limit);

		return $qb->getQuery()->getResult();
	}

	private function createPublishedQb()
	{
		$qb = $this->createQueryBuilder('p');
		$qb->andWhere('p.published = TRUE');
		$qb->andWhere('p.deleted = FALSE');
		return $qb;
	}

	public function findNotDeleted($id)
	{
		return $this->findOneBy([
			'id' => $id,
			'deleted' => FALSE,
		]);
	}

}