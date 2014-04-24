<?php

namespace Presidos\Presentation;

use Doctrine\ORM\Mapping as ORM;
use Presidos\Doctrine\Entity;

/**
 * @author Jan Marek
 *
 * @ORM\Table(name="uploaded_image")
 * @ORM\Entity(repositoryClass="Presidos\Presentation\UploadedImageRepository")
 */
class UploadedImage extends Entity
{

	/** @ORM\Column(type="string") */
	private $name;

	/** @ORM\ManyToOne(targetEntity="Presentation") */
	private $presentation;

	public function __construct(Presentation $presentation)
	{
		$this->presentation = $presentation;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getPresentation()
	{
		return $this->presentation;
	}

}