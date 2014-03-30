<?php

namespace Presidos\Presentation;

use Doctrine\ORM\Mapping as ORM;
use Presidos\Doctrine\Entity;
use Presidos\Doctrine\Timestampable;
use Presidos\User\User;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="presentation")
 * @ORM\Entity(repositoryClass="Presidos\Presentation\PresentationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Presentation extends Entity implements \JsonSerializable
{

	use Timestampable;

	/** @ORM\Column(type="string", nullable=true) */
	private $name;

	/** @ORM\Column(type="text", nullable=true) */
	private $texy;

	/** @ORM\ManyToOne(targetEntity="Presidos\User\User") */
	private $user;

	/** @ORM\ManyToOne(targetEntity="Presidos\Presentation\Theme") */
	private $theme;

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

	public function setTheme(Theme $theme)
	{
		$this->theme = $theme;
	}

	public function getTheme()
	{
		return $this->theme;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'texy' => $this->texy,
			'theme' => $this->theme,
			'updated' => $this->getUpdated(),
			'created' => $this->getCreated(),
		];
	}

}