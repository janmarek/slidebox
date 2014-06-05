<?php

namespace Presidos\User\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Security\AuthenticationException;
use Presidos\Presenter\BasePresenter;
use Presidos\User\Email\ForgottenEmailFactory;
use Presidos\User\Email\RegisterEmailFactory;
use Presidos\User\FacebookAuthenticator;
use Presidos\User\PasswordAuthenticator;
use Presidos\User\User;
use Presidos\User\UserRepository;

class LoginPresenter extends BasePresenter
{

	/** @var PasswordAuthenticator */
	private $passwordAuthenticator;

	public function __construct(PasswordAuthenticator $passwordAuthenticator)
	{
		$this->passwordAuthenticator = $passwordAuthenticator;
	}

	public function actionDefault($backlink = NULL)
	{
	}

	protected function createComponentLogInForm()
	{
		$form = new Form;
		$form->addText('email', 'Email:')
			->setRequired('Fill your email.');

		$form->addPassword('password', 'Password:')
			->setRequired('Fill your password.');

		$form->addSubmit('s', 'Login');

		$form->onSuccess[] = array($this, 'logInFormSubmitted');

		return $form;
	}

	public function logInFormSubmitted($form)
	{
		try {
			$values = $form->getValues();
			$identity = $this->passwordAuthenticator->authenticate($values->email, $values->password);
			$this->user->login($identity);

			$this->restoreRequest($this->getParameter('backlink'));
			$this->flashMessage('You have been successfully logged in.');
			$this->redirect(':Presentation:List:');

		} catch (AuthenticationException $e) {
			$form->addError('You have filled wrong email or password.');
		}
	}

	public function actionLogout()
	{
		$this->getUser()->logout(TRUE);
		$this->flashMessage('You have been successfully logged out.');
		$this->redirect(':Homepage:');
	}

}
