<?php

namespace Presidos\Presenter;

use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	protected function afterRender()
	{
		parent::afterRender();
	}

}
