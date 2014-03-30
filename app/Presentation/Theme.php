<?php

namespace Presidos\Presentation;

use Doctrine\ORM\Mapping as ORM;
use Presidos\Doctrine\Entity;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="Presidos\Presentation\ThemeRepository")
 */
class Theme extends Entity implements \JsonSerializable
{

	/** @ORM\Column(type="string") */
	private $name;

	/** @ORM\Column(type="string", name="class_name") */
	private $className;

	/** @ORM\Column(type="boolean") */
	private $public;

	/** @ORM\ManyToOne(targetEntity="Presidos\User\User") */
	private $user;

	public function __construct()
	{
		$this->public = FALSE;
	}

	public function setClassName($className)
	{
		$this->className = $className;
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setPublic($public)
	{
		$this->public = $public;
	}

	public function getPublic()
	{
		return $this->public;
	}

	public function setUser($user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'public' => $this->public,
			'className' => $this->className,
		];
	}

}