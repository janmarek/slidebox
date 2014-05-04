<?php

namespace Presidos\Presentation\Presenter;

use Nette\InvalidStateException;
use Nette\Utils\Strings;
use Presidos\Presentation\Generator\Generator;
use Presidos\Presentation\Generator\TexyFactory;
use Presidos\Presentation\Generator\WkHtmlToPdfFactory;
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

	/** @var WkHtmlToPdfFactory */
	private $wkHtmlToPdfFactory;

	public function __construct(PresentationRepository $presentationRepository, Generator $generator,
		TexyFactory $texyFactory, WkHtmlToPdfFactory $wkHtmlToPdfFactory)
	{
		$this->presentationRepository = $presentationRepository;
		$this->generator = $generator;
		$this->texyFactory = $texyFactory;
		$this->wkHtmlToPdfFactory = $wkHtmlToPdfFactory;
	}

	public function renderDefault($id, $format = 'pdf')
	{
		$presentation = $this->presentationRepository->find($id);
		$this->checkExistence($presentation);

		if (!$presentation->isPublished() && !$presentation->isEditableBy($this->getUser()->getIdentity())) {
			$this->error('Unauthorized access to presentation.', 403);
		}

		$template = $this->createTemplate()->setFile(__DIR__ . '/../templates/Pdf/default.latte');
		$template->presentation = $presentation;
		$texy = $presentation->getTexy();
		$html = $this->texyFactory->createPdfTexy()->process($texy);
		$template->html = $this->generator->getPresentation($html)->getHtml();

		if ($format === 'pdf') {
			$pdf = $this->wkHtmlToPdfFactory->create();
			$pdf->addPage((string) $template);

			if (!$pdf->send('presentation-' . Strings::webalize($presentation->getName()) . '.pdf')) {
				throw new InvalidStateException($pdf->getError());
			}
		} elseif ($format === 'html') {
			echo $template;
		}

		$this->terminate();
	}

}
