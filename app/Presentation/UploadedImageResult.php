<?php

namespace SlideBox\Presentation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jan Marek
 */
class UploadedImageResult
{

	private $errors = [];

	private $url;

	private $image;

	public function addError($error)
	{
		$this->errors[] = $error;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setImage(UploadedImage $image)
	{
		$this->image = $image;
	}

	public function getImage()
	{
		return $this->image;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

}