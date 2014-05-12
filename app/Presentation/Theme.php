<?php

namespace Presidos\Presentation;

use Doctrine\Common\Collections\ArrayCollection;
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

	/** @ORM\Column(type="string", name="class_name", unique=true) */
	private $className;

	/** @ORM\OneToMany(targetEntity="ThemeVariant", mappedBy="theme", cascade={"persist"}) */
	private $variants;

	public function __construct($name, $className)
	{
		$this->variants = new ArrayCollection();
		$this->name = $name;
		$this->className = $className;
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getVariants()
	{
		return $this->variants->toArray();
	}

	/**
	 * @param string $name
	 * @param string $className
	 * @param string $mainColor
	 * @return ThemeVariant
	 */
	public function addVariant($name, $className, $mainColor)
	{
		$variant = new ThemeVariant($this, $name, $className, $mainColor);
		$this->variants->add($variant);

		return $variant;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'className' => $this->className,
			'variants' => $this->getVariants(),
		];
	}

}