<?php

namespace Presidos\Presentation\Presenter;

use Nette\InvalidStateException;
use Presidos\Presentation\Generator\GeneratorTexy;
use Presidos\Presentation\HtmlGenerator;
use Presidos\Presentation\PresentationRepository;
use Presidos\Presenter\BasePresenter;

class PdfPresenter extends BasePresenter
{

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var GeneratorTexy */
	private $generatorTexy;

	public function __construct(PresentationRepository $presentationRepository, GeneratorTexy $generatorTexy)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generatorTexy = $generatorTexy;
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
		$template->html = $this->generatorTexy->process($presentation->getTexy());

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
