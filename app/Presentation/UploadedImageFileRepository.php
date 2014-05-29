<?php

namespace Presidos\Presentation;

use Doctrine\ORM\EntityManager;
use Nette\Http\FileUpload;
use Nette\Image;
use Nette\Utils\Strings;

/**
 * @author Jan Marek
 */
class UploadedImageFileRepository
{

	const MAX_WIDTH = 2000;

	const MAX_HEIGHT = 1500;

	const QUALITY = 80;

	/** @var EntityManager */
	private $em;

	private $rootDir;

	private $urlPrefix;

	public function __construct(EntityManager $em, $rootDir, $urlPrefix)
	{
		$this->em = $em;
		$this->rootDir = $rootDir;
		$this->urlPrefix = $urlPrefix;
	}

	/**
	 * @param Presentation $presentation
	 * @param FileUpload $upload
	 * @return UploadedImageResult
	 */
	public function upload(Presentation $presentation, FileUpload $upload)
	{
		$result = new UploadedImageResult();

		if (!$upload->isOk()) {
			$result->addError('Image has not been uploaded successfully.');
			return $result;
		}

		if (!$upload->isImage()) {
			$result->addError('File is not image.');
			return $result;
		}

		// get image from file
		$image = $upload->toImage()->resize(self::MAX_WIDTH, self::MAX_HEIGHT, Image::SHRINK_ONLY);

		if ($upload->getImageSize()[2] === IMG_JPEG) {
			$extension = 'jpg';
		} else {
			$extension = 'png';
		}

		// save to database
		$entity = new UploadedImage($presentation);
		$entity->setName(pathinfo($upload->getSanitizedName(), PATHINFO_FILENAME) . '.' . $extension);
		$this->em->persist($entity);
		$this->em->flush();

		// save file
		$path = $this->getPath($entity);
		@mkdir(pathinfo($path, PATHINFO_DIRNAME));
		$image->save($path, self::QUALITY);

		$result->setUrl($this->getUrl($entity));
		$result->setImage($entity);

		return $result;
	}

	public function getFullKey(UploadedImage $image)
	{
		return $image->getPresentation()->getUser()->getId() . '/' . $image->getId() . '-' . $image->getName();
	}

	public function getPath(UploadedImage $image)
	{
		return $this->rootDir . '/' . $this->getFullKey($image);
	}

	public function getUrl(UploadedImage $image)
	{
		return $this->urlPrefix . '/' . $this->getFullKey($image);
	}

} 