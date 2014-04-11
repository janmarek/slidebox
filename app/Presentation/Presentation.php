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

	/** @ORM\Column(type="boolean") */
	private $nameLocked;

	/** @ORM\Column(type="text", nullable=true) */
	private $texy;

	/** @ORM\Column(type="boolean") */
	private $published;

	/** @ORM\ManyToOne(targetEntity="Presidos\User\User") */
	private $user;

	/** @ORM\ManyToOne(targetEntity="Presidos\Presentation\Theme") */
	private $theme;

	public function __construct(User $user)
	{
		$this->user = $user;
		$this->published = FALSE;
		$this->nameLocked = FALSE;
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

	public function setPublished($published)
	{
		$this->published = $published;
	}

	public function isPublished()
	{
		return $this->published;
	}

	public function isNameLocked()
	{
		return $this->nameLocked;
	}

	public function lockName()
	{
		$this->nameLocked = TRUE;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'nameLocked' => $this->nameLocked,
			'texy' => $this->texy,
			'theme' => $this->theme,
			'published' => $this->published,
			'updated' => $this->getUpdated()->format('c'),
			'created' => $this->getCreated()->format('c'),
		];
	}

}