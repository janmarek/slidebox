<?php

namespace Presidos\Test\Presentation;

use Nette\Http\FileUpload;
use Nette\Image;
use Presidos\Presentation\Presentation;
use Presidos\Presentation\UploadedImageFileRepository;
use Presidos\Test\IntegrationTestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @author Jan Marek
 * @testCase
 */
class UploadedImageFileRepositoryTest extends IntegrationTestCase
{

	/** @var UploadedImageFileRepository */
	private $repository;

	/** @var Presentation */
	private $presentation;

	public function setUp()
	{
		parent::setUp();

		@mkdir($this->getContainer()->parameters['tempDir'] . '/tests');
		$rootDir = $this->getContainer()->parameters['tempDir'] . '/tests/' . getmypid();
		\Tester\Helpers::purge($rootDir);
		$this->repository = new UploadedImageFileRepository(
			$this->getContainer()->em,
			$rootDir,
			'http://files.presidos.dev'
		);

		$this->presentation = $this->getContainer()->presentationRepository->findOneBy(['name' => 'Presentation 1']);
	}

	public function testUploadNotOk()
	{
		$notOk = $this->mockista->create(FileUpload::class, ['isOk' => FALSE]);
		$result = $this->repository->upload($this->presentation, $notOk);

		Assert::equal(['Image has not been uploaded successfully.'], $result->getErrors());
	}

	public function testUploadNotImage()
	{
		$notOk = $this->mockista->create(FileUpload::class, ['isOk' => TRUE, 'isImage' => FALSE]);
		$result = $this->repository->upload($this->presentation, $notOk);

		Assert::equal(['File is not image.'], $result->getErrors());
	}

	public function testUploadOk()
	{
		$ok = $this->mockista->create(FileUpload::class, [
			'isOk' => TRUE,
			'isImage' => TRUE,
			'toImage' => Image::fromFile(__DIR__ . '/example.jpg'),
			'getSanitizedName' => 'example.jpg',
		]);

		$result = $this->repository->upload($this->presentation, $ok);

		Assert::equal([], $result->getErrors());
		$image = $result->getImage();
		Assert::equal(
			'http://files.presidos.dev/' . $this->presentation->getUser()->getId() . '/' . $image->getId() . '-example.jpg',
			$result->getUrl()
		);

		// test resize
		$netteImage = Image::fromFile($this->repository->getPath($image));
		Assert::equal(2000, $netteImage->getWidth());
		Assert::equal(1333, $netteImage->getHeight());
	}

}

(new UploadedImageFileRepositoryTest())->run();