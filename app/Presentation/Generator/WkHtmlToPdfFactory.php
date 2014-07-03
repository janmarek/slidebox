<?php

namespace SlideBox\Presentation\Generator;

/**
 * @author Jan Marek
 */
class WkHtmlToPdfFactory
{

	private $options;

	private $tempDir;

	public function __construct($tempDir, $options = [])
	{
		$this->options = $options;
		$this->tempDir = $tempDir;
	}

	public function create()
	{
		if (!is_writable($this->tempDir)) {
			throw new \Nette\InvalidStateException('Directory ' . $this->tempDir . ' is not writable.');
		}

		$defaults = [
			'margin-bottom' => 0,
			'margin-top' => 0,
			'margin-left' => 0,
			'margin-right' => 0,
			'page-width' => '300',
			'page-height' => '225',
			'tmp' => $this->tempDir,
		];

		$options = array_merge($defaults, $this->options);

		return new \WkHtmlToPdf($options);
	}

} 