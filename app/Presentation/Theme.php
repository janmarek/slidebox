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

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'className' => $this->className,
		];
	}

}