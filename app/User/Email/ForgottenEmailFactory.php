<?php

namespace Presidos\User\Email;

use Nette\Mail\Message;
use Presidos\User\User;
use Presidos\Presenter\BasePresenter;

/**
 * @author Jan Marek
 */
class ForgottenEmailFactory
{

	private $fromEmail;

	private $fromName;

	public function __construct($fromEmail, $fromName)
	{
		$this->fromEmail = $fromEmail;
		$this->fromName = $fromName;
	}

	/**
	 * @param BasePresenter $presenter
	 * @param User $registeredUser
	 * @return Message
	 */
	public function create(BasePresenter $presenter, User $registeredUser)
	{
		$message = new Message();
		$message->setFrom($this->fromEmail, $this->fromName);
		$message->addTo($registeredUser->getEmail(), $registeredUser->getName());

		$template = $presenter->createTemplate();
		$template->setFile(__DIR__ . '/ForgottenEmail.latte');
		$template->registeredUser = $registeredUser;

		$message->setHtmlBody($template);

		return $message;
	}

}