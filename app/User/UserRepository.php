<?php

namespace Presidos\User;

use Presidos\Model\Doctrine\Repository;

/**
 * @author Jan Marek
 */
class UserRepository extends Repository
{

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