<?php

namespace SlideBox\Presentation;

use SlideBox\Doctrine\Repository;

/**
 * @author Jan Marek
 */
class UploadedImageRepository extends Repository
{

	/**
	 * @param Presentation $presentation
	 * @return UploadedImage[]
	 */
	public function findByPresentation(Presentation $presentation)
	{
		return $this->findBy([
			'presentation' => $presentation,
		], [
			'name' => 'asc',
		]);
	}

}