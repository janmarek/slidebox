<?php

namespace Presidos\Presentation;

/**
 * @author Jan Marek
 */
class HtmlGeneratorResult
{

	private $html;

	private $name;

	public function __construct($html, $name)
	{
		$this->html = $html;
		$this->name = $name;
	}

	public function getHtml()
	{
		return $this->html;
	}

	public function getName()
	{
		return $this->name;
	}

} 