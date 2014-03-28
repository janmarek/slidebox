<?php

namespace Presidos\Model\Presentation;

use Presidos\Model\Doctrine\Repository;
use Presidos\Model\User\User;

/**
 * @author Jan Marek
 */
class PresentationRepository extends Repository
{

	public function findByUserAndId(User $user, $id)
	{
		return $this->findOneBy([
			'user' => $user,
			'id' => $id,
		]);
	}

}