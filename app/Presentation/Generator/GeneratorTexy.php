<?php

namespace Presidos\Presentation\Generator;

/**
 * @author Jan Marek
 */
class GeneratorTexy extends \Texy
{

	/** @var SlidesTexyHandler */
	private $slidesTexyHandler;
	
	public function __construct(SlidesTexyHandler $slidesHandler)
	{
		parent::__construct();
		$this->slidesTexyHandler = $slidesHandler;
		$this->addHandler('afterParse', $this->slidesTexyHandler);
	}

	/**
	 * @return Presentation
	 */
	public function getLastPresentation()
	{
		return $this->slidesTexyHandler->getPresentation();
	}

} 