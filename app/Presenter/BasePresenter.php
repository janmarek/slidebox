<?php

namespace Presidos\Presenter;

use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	/** @var \WebLoader\LoaderFactory @inject */
	public $webLoader;

//	public function formatLayoutTemplateFiles()
//	{
//		return array(
//			dirname($this->getReflection()->getFileName()) . '/layout.latte',
//			$this->context->parameters['appDir'] . '/presenters/layout.latte',
//		);
//	}
//
//	public function formatTemplateFiles()
//	{
//		return array(
//			dirname($this->getReflection()->getFileName()) . '/' . $this->getView() . '.latte',
//		);
//	}

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
