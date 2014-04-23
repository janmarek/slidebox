<?php

namespace Presidos\Presentation\Presenter;

use Nette\InvalidStateException;
use Presidos\Presentation\Generator\Generator;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PdfPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var Generator */
	private $generator;

	public function __construct(PresentationRepository $presentationRepository, Generator $generator)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generator = $generator;
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
		$template->html = $this->generator->getPresentation($presentation->getTexy())->getHtml();

		if ($format === 'pdf') {
			$pdf = new \WkHtmlToPdf([
				'binPath' => '/usr/local/bin/wkhtmltopdf',
				'margin-bottom' => 0,
				'margin-top' => 0,
				'margin-left' => 0,
				'margin-right' => 0,
				'page-width' => '300',
				'page-height' => '225',
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
