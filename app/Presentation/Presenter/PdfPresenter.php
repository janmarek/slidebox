<?php

namespace Presidos\Presentation\Presenter;

use Nette\InvalidStateException;
use Presidos\Presentation\Generator\Generator;
use Presidos\Presentation\Generator\TexyFactory;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PdfPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var Generator */
	private $generator;

	/** @var TexyFactory */
	private $texyFactory;

	public function __construct(PresentationRepository $presentationRepository, Generator $generator, TexyFactory $texyFactory)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generator = $generator;
		$this->texyFactory = $texyFactory;
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
		$texy = $presentation->getTexy();
		$html = $this->texyFactory->createPdfTexy()->process($texy);
		$template->html = $this->generator->getPresentation($html)->getHtml();

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
