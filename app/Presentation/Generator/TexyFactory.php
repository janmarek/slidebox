<?php

namespace Presidos\Presentation\Generator;

use Nette\Utils\Strings;
use Texy;
use TexyConfigurator;
use TexyHtml;

/**
 * @author Jan Marek
 */
class TexyFactory
{

	const REGEXP_YOUTUBE = '/^youtube:(.*)$/';

	/**
	 * @return Texy
	 */
	private function createPlainTexy()
	{
		$texy = new Texy();
		TexyConfigurator::safeMode($texy);
		$texy->allowedTags['iframe'] = ['width', 'height', 'src', 'frameborder', 'allowfullscreen'];
		$texy->urlSchemeFilters[Texy::FILTER_IMAGE] = NULL;

		$texy->tabWidth = 4;
		$texy->headingModule->balancing = 'fixed';

		$texy->imageModule->leftClass = 'image-left';
		$texy->imageModule->rightClass = 'image-right';
		$texy->figureModule->leftClass = 'figure-left';
		$texy->figureModule->rightClass = 'figure-right';

		$texy->allowedClasses = Texy::ALL;
		$texy->allowed['image'] = TRUE;
		$texy->linkModule->forceNoFollow = FALSE;

		return $texy;
	}

	/**
	 * @return Texy
	 */
	public function createTexy()
	{
		$texy = $this->createPlainTexy();
		$texy->addHandler('image', [$this, 'youtubeHandler']);

		return $texy;
	}

	/**
	 * @return Texy
	 */
	public function createPdfTexy()
	{
		$texy = $this->createPlainTexy();
		$texy->addHandler('image', [$this, 'youtubePdfHandler']);

		return $texy;
	}

	public function youtubeHandler($invocation, $image, $link)
	{
		$matches = Strings::match($image->URL, self::REGEXP_YOUTUBE);

		if ($matches) {
			$video = $matches[1];

			return TexyHtml::el('iframe', [
				'width' => $image->width ?: 560,
				'height' => $image->height ?: 315,
				'src' => '//www.youtube.com/embed/' . $video,
				'frameborder' => 0,
				'allowfullscreen' => TRUE,
			]);
		} else {
			return $invocation->proceed();
		}
	}

	public function youtubePdfHandler($invocation, $image, $link)
	{
		$matches = Strings::match($image->URL, self::REGEXP_YOUTUBE);

		if ($matches) {
			$a = TexyHtml::el('a', ['href' => 'http://www.youtube.com/watch?v=' . $matches[1]]);
			$a->add(TexyHtml::el('img', [
				'src' => 'http://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg',
				'alt' => '',
				'width' => $image->width,
				'height' => $image->height,
			]));

			return $a;
		}

		return $invocation->proceed();
	}

} 