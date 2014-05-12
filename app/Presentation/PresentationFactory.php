<?php

namespace Presidos\Presentation;

use Presidos\User\User;

/**
 * @author Jan Marek
 */
class PresentationFactory
{

	/**
	 * @var ThemeVariantRepository
	 */
	private $themeVariantRepository;

	public function __construct(ThemeVariantRepository $themeVariantRepository)
	{
		$this->themeVariantRepository = $themeVariantRepository;
	}

	public function create(User $user)
	{
		$presentation = new Presentation($user);
		$presentation->setName('New Presentation');
		$presentation->setTexy(file_get_contents(__DIR__ . '/defaultPresentation.texy'));
		$presentation->setThemeVariant($this->themeVariantRepository->getDefault());

		return $presentation;
	}

} 