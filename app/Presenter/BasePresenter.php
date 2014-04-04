<?php

namespace Presidos\Presenter;

use Nette\Application\UI\Presenter;
use Nextras\Application\UI\SecuredLinksPresenterTrait;

abstract class BasePresenter extends Presenter
{

	use SecuredLinksPresenterTrait;

	public function createTemplate($class = NULL)
	{
		return parent::createTemplate();
	}

	protected function afterRender()
	{
		parent::afterRender();
	}

	/**
	 * Throw 404 exception if object does not exist
	 *
	 * @param $object
	 */
	public function checkExistence($object)
	{
		if (!$object) {
			$this->error();
		}
	}

	protected function checkLoggedIn()
	{
		if (!$this->user->isLoggedIn()) {
			$backlink = $this->storeRequest();
			$this->redirect(':User:Login:', ['backlink' => $backlink]);
		}
	}

}
