<?php

namespace Presidos\Presenter;

use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Utils\Arrays;
use Nextras\Application\UI\SecuredLinksPresenterTrait;

abstract class BasePresenter extends Presenter
{

	use SecuredLinksPresenterTrait
	{
		getCsrfToken as traitGetCsrfToken;
	}

	public $testMode = FALSE;

	public $testPresenterName = NULL;

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

	protected function getPostParameter($name)
	{
		return Arrays::get($this->getRequest()->getPost(), $name, NULL);
	}

	public function getCsrfToken($control, $method, $params)
	{
		if ($this->testMode) {
			return 'csrf';
		} else {
			return $this->traitGetCsrfToken($control, $method, $params);
		}
	}

	public function flashMessage($message, $type = 'info')
	{
		if (!$this->testMode) {
			parent::flashMessage($message, $type);
		}
	}

	// test helper methods

	public function runPost($action, $get = [], $post = [])
	{
		$get['action'] = $action;
		return $this->run(new Request($this->testPresenterName, 'POST', $get, $post));
	}

	public function runGet($action, $get = [])
	{
		$get['action'] = $action;
		return $this->run(new Request($this->testPresenterName, 'GET', $get));
	}

}
