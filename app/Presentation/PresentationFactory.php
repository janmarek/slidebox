<?php

namespace Presidos\Presentation;

use Presidos\User\User;

/**
 * @author Jan Marek
 */
class PresentationFactory
{

	/**
	 * @var ThemeRepository
	 */
	private $themeRepository;

	public function __construct(ThemeRepository $themeRepository)
	{
		$this->themeRepository = $themeRepository;
	}

	public function create(User $user)
	{
		$presentation = new Presentation($user);
		$presentation->setName('New Presentation');
		$presentation->setTexy(file_get_contents(__DIR__ . '/defaultPresentation.texy'));
		$presentation->setTheme($this->themeRepository->getDefaultTheme());

		return $presentation;
	}

} 