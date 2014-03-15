<?php

namespace Presidos\Presenter;

use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	/** @var \WebLoader\LoaderFactory @inject */
	public $webLoader;

	protected function afterRender()
	{
		parent::afterRender();
	}

	/** @return CssLoader */
	protected function createComponentCss()
	{
		return $this->webLoader->createCssLoader('default');
	}

	/** @return JavaScriptLoader */
	protected function createComponentJs()
	{
		return $this->webLoader->createJavaScriptLoader('default');
	}

}
