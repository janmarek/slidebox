<?php

namespace Presidos\Presenter;

use Nette\Application\BadRequestException;
use Nette\Diagnostics\Debugger;

class ErrorPresenter extends BasePresenter
{

	public function renderDefault($exception)
	{
		if (!($exception instanceof BadRequestException)) {
			Debugger::log($exception, Debugger::ERROR);
		}

		if ($this->isAjax()) {
			$this->payload->error = TRUE;
			$this->terminate();
		} elseif ($exception instanceof BadRequestException) {
			$this->setView('404');
		} else {
			$this->setView('500');
		}
	}

}