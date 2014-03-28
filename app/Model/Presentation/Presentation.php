<?php

namespace Presidos\Model\Presentation;

use Doctrine\ORM\Mapping as ORM;
use Presidos\Model\Doctrine\Entity;
use Presidos\Model\Doctrine\Timestampable;
use Presidos\User\User;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="presentation")
 * @ORM\Entity(repositoryClass="Presidos\Model\Presentation\PresentationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Presentation extends Entity
{

	use Timestampable;

	/** @ORM\Column(type="string", nullable=true) */
	private $name;

	/** @ORM\Column(type="text", nullable=true) */
	private $texy;

	/** @ORM\ManyToOne(targetEntity="Presidos\User\User") */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;
		$this->initDateTimes();
	}

	public function setName($name)
	{
		$this->name = $name ?: NULL;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setTexy($texy)
	{
		$this->texy = $texy ?: NULL;
	}

	public function getTexy()
	{
		return $this->texy;
	}

	public function getUser()
	{
		return $this->user;
	}

}