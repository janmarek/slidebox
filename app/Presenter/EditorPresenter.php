<?php

namespace Presidos\Presenter;

use Presidos\Model\HtmlGenerator;

class EditorPresenter extends BasePresenter
{

	/**
	 * @var HtmlGenerator
	 */
	private $htmlGenerator;

	public function __construct(HtmlGenerator $htmlGenerator)
	{
		$this->htmlGenerator = $htmlGenerator;
	}

	public function renderDefault()
	{
	}

	public function renderPreview()
	{
		$texy = $this->getHttpRequest()->getPost('text');

		$this->sendJson([
			'html' => $this->htmlGenerator->getPresentationHtml($texy)
		]);
	}

}
