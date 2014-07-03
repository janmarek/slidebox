<?php

namespace SlideBox\Presentation\Presenter;

use SlideBox\Presenter\BasePresenter;
use SlideBox\User\UserRepository;

/**
 * @author Jan Marek
 */
class AutocompletePresenter extends BasePresenter
{

	private $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function renderCollaborators($name, $collaboratorIds = [])
	{
		$forbiddenIds = array_map('intval', $collaboratorIds);
		$forbiddenIds[] = $this->getUser()->getId();

		$users = $this->userRepository->autocompleteUsers($name, $forbiddenIds);

		$this->sendJson([
			'users' => $users,
		]);
	}

} 