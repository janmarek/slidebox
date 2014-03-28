<?php

namespace Presidos\Presenter;

class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		dump($this->context->userRepository->findAll());
	}

}
