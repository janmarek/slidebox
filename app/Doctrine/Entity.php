<?php

namespace Presidos\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;

/**
 * @ORM\MappedSuperclass
 */
class Entity extends Object
{

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	public function getId()
	{
		return $this->id;
	}

}