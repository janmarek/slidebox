<?php

namespace Presidos\Presentation\Presenter;

use Nette\InvalidStateException;
use Presidos\Presentation\HtmlGenerator;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PdfPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var HtmlGenerator */
	private $htmlGenerator;

	public function __construct(PresentationRepository $presentationRepository, HtmlGenerator $htmlGenerator)
	{
		$this->presentationRepository = $presentationRepository;
		$this->htmlGenerator = $htmlGenerator;
	}

	public function renderDefault($id, $format = 'pdf')
	{
		$presentation = $this->presentationRepository->find($id);
		$this->checkExistence($presentation);

		if (!$presentation->isPublished() && $this->getUser()->getIdentity() !== $presentation->getUser()) {
			$this->error('Unauthorized access to presentation.', 403);
		}

		$template = $this->createTemplate()->setFile(__DIR__ . '/../templates/Pdf/default.latte');
		$template->presentation = $presentation;
		$template->html = $this->htmlGenerator->getPresentationHtml($presentation->getTexy())->getHtml();

		if ($format === 'pdf') {
			$pdf = new \WkHtmlToPdf([
				'binPath' => '/usr/local/bin/wkhtmltopdf'
			]);
			$pdf->addPage((string) $template);

			if (!$pdf->send('presentation.pdf')) {
				throw new InvalidStateException($pdf->getError());
			}
		} elseif ($format === 'html') {
			echo $template;
		}

		$this->terminate();
	}

}
