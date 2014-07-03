<?php

namespace SlideBox\User;

use Nette\Utils\Strings;
use SlideBox\Doctrine\Repository;
use SlideBox\Presentation\Presentation;

/**
 * @author Jan Marek
 */
class UserRepository extends Repository
{

	public function autocompleteUsers($string, $forbiddenIds)
	{
		$qb = $this->createQueryBuilder('u');

		if (!empty($forbiddenIds)) {
			$qb->andWhere('u.id not in (:collaborators)')->setParameter('collaborators', $forbiddenIds);
		}

		$qb->andWhere('u.allowed = true');
		$qb->andWhere('lower(u.name) like :name or lower(u.email) like :name')
			->setParameter('name', Strings::lower($string) . '%');
		$qb->setMaxResults(10);

		return $qb->getQuery()->getResult();
	}

	public function findAllowedByIds($ids)
	{
		if (empty($ids)) {
			return [];
		}

		$qb = $this->createQueryBuilder('u');
		$qb->andWhere('u.id in (:ids)')->setParameter('ids', $ids);
		$qb->andWhere('u.allowed = TRUE');

		return $qb->getQuery()->getResult();
	}

	public function findAllowedById($id)
	{
		return $this->findOneBy(array(
			'id' => $id,
			'allowed' => TRUE,
		));
	}

	public function findAllowedByEmail($mail)
	{
		return $this->findOneBy(array(
			'email' => $mail,
			'allowed' => TRUE,
		));
	}

	public function hasUniqueEmail(User $user)
	{
		return $this->hasUniqueField($user->getId(), $user->getEmail(), 'email');
	}

	public function hasUniqueUsername(User $user)
	{
		return $this->hasUniqueField($user->getId(), $user->getName(), 'name');
	}

	public function hasUniqueFacebook(User $user)
	{
		return $this->hasUniqueField($user->getId(), $user->getFacebookUid(), 'facebookUid');
	}
	
	private function hasUniqueField($id, $value, $field)
	{
		$qb = $this->createQueryBuilder('u')
			->select('count(u.id)')
			->where('lower(u.' . $field . ') = :val');

		$qb->setParameter('val', \Nette\Utils\Strings::lower($value));

		if ($id !== NULL) {
			$qb->andWhere('u.id <> :id');
			$qb->setParameter('id', $id);
		}

		return $qb->getQuery()->getSingleScalarResult() == 0;
	}

	public function findByEmailAndHash($email, $hash)
	{
		if (!$hash) {
			throw new \InvalidArgumentException('Hash has to be filled.');
		}

		return $this->findOneBy(array('email' => $email, 'hash' => $hash));
	}

	public function findByEmail($email)
	{
		return $this->findOneBy(array('email' => $email));
	}

}