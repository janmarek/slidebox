<?php

namespace Presidos\Presenter;

use Nette\Diagnostics\Debugger;

class ErrorPresenter extends BasePresenter
{

	public function renderDefault($exception)
	{
		if ($this->isAjax()) {
			$this->payload->error = TRUE;
			$this->terminate();
		} elseif ($exception instanceof \Nette\Application\BadRequestException) {
			$this->setView('404');
		} else {
			$this->setView('500');
			Debugger::log($exception, Debugger::ERROR);
		}
	}

}