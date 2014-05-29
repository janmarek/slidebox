<?php

namespace Presidos\Presentation;

use Doctrine\ORM\Mapping as ORM;
use Presidos\Doctrine\Entity;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="theme_variant")
 * @ORM\Entity(repositoryClass="Presidos\Presentation\ThemeVariantRepository")
 */
class ThemeVariant extends Entity implements \JsonSerializable
{

	/** @ORM\Column(type="string") */
	private $name;

	/** @ORM\Column(type="string", name="main_color", length=6) */
	private $mainColor;

	/** @ORM\Column(type="string", name="class_name", unique=true) */
	private $className;

	/** @ORM\ManyToOne(targetEntity="Theme") */
	private $theme;

	public function __construct(Theme $theme, $name, $className, $mainColor)
	{
		$this->theme = $theme;
		$this->name = $name;
		$this->className = $className;
		$this->mainColor = $mainColor;
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getTheme()
	{
		return $this->theme;
	}

	public function getMainColor()
	{
		return $this->mainColor;
	}

	public function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->name,
			'mainColor' => $this->mainColor,
			'className' => $this->className,
		];
	}

}