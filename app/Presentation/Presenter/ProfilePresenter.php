<?php

namespace SlideBox\Presentation\Presenter;

use Doctrine\ORM\EntityManager;
use SlideBox\Presentation\Control\PresentationPreviewPresenterFactory;
use SlideBox\Presentation\PresentationFactory;
use SlideBox\Presentation\PresentationRepository;
use SlideBox\Presenter\BasePresenter;
use SlideBox\User\UserRepository;

class ProfilePresenter extends BasePresenter
{

	use PresentationPreviewPresenterFactory;

	/** @var PresentationRepository */
	private $presentationRepository;

	/** @var UserRepository */
	private $userRepository;

	public function __construct(PresentationRepository $presentationRepository, UserRepository $userRepository)
	{
		$this->presentationRepository = $presentationRepository;
		$this->userRepository = $userRepository;
	}

	public function renderDefault($id)
	{
		$user = $this->userRepository->findAllowedById($id);
		$this->checkExistence($user);

		$presentations = $this->presentationRepository->findPublishedByUser($user);
		$this->template->presentations = $presentations;
		$this->template->currentUser = $user;
	}

}
