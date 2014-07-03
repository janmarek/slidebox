<?php

namespace SlideBox\Presentation;

use SlideBox\Doctrine\Repository;

/**
 * @author Jan Marek
 */
class ThemeRepository extends Repository
{

	public function getAll()
	{
		$qb = $this->createQueryBuilder('t');
		$qb->leftJoin('t.variants', 'v');
		$qb->addSelect('v');
		$qb->orderBy('t.id, v.id');

		return $qb->getQuery()->getResult();
	}

}