<?php

namespace Presidos\User\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Presidos\Presenter\BasePresenter;
use Presidos\User\Email\ForgottenEmailFactory;
use Presidos\User\UserRepository;

class PasswordPresenter extends BasePresenter
{

	/** @var EntityManager */
	private $em;

	/** @var UserRepository */
	private $userRepository;

	/** @var ForgottenEmailFactory */
	private $forgottenEmailFactory;

	/** @var IMailer */
	private $mailer;

	public function __construct(EntityManager $em, UserRepository $userRepository,
		ForgottenEmailFactory $forgottenEmailFactory, IMailer $mailer)
	{
		$this->em = $em;
		$this->userRepository = $userRepository;
		$this->forgottenEmailFactory = $forgottenEmailFactory;
		$this->mailer = $mailer;
	}

	public function renderDefault()
	{

	}

	protected function createComponentSendNewPasswordForm()
	{
		$form = new Form();
		$form->addText('email', 'E-mail:')
			->setRequired('Please fill your email.')
			->addRule(Form::EMAIL, 'Please fill your email correctly.')
			->getControlPrototype()->class('form-control');
		$form->addSubmit('s', 'Reset password')
			->getControlPrototype()->class('btn btn-primary');
		$form->onSuccess[] = $this->submitSendNewPasswordForm;

		return $form;
	}

	public function submitSendNewPasswordForm(Form $form)
	{
		$user = $this->userRepository->findAllowedByEmail($form->values->email);

		if (!$user) {
			$form->addError('User ' . $form->values->email . ' has not been found.');
			return;
		}

		$user->changeHash();

		$this->em->flush();

		$message = $this->forgottenEmailFactory->create($this, $user);
		$this->mailer->send($message);

		$this->flashMessage('Email with instructions to set new password has been successfully changed.');
		$this->redirect(':Homepage:');
	}

	public function renderNew($email, $hash)
	{
		$user = $this->userRepository->findByEmailAndHash($email, $hash);

		if (!$user) {
			$this->flashMessage('User has not been found. Maybe password has been already changed.', 'error');
			$this->redirect(':Homepage:');
		}
	}

	protected function createComponentNewPasswordForm()
	{
		$form = new Form();
		$form->addPassword('password', 'New password:')
			->setRequired('Please fill password.');
		$form->addPassword('password2', 'Password one more time:')
			->setRequired('Please insert your password one more time.')
			->addRule(Form::EQUAL, 'Passwords has to match.', $form['password']);
		$form->addSubmit('s', 'Change password')
			->getControlPrototype()->class('btn btn-primary');
		$form->onSuccess[] = $this->submitNewPasswordForm;

		return $form;
	}

	public function submitNewPasswordForm(Form $form)
	{
		$user = $this->userRepository->findByEmail($this->getParameter('email'), $this->getParameter('hash'));
		$this->checkExistence($user);

		$user->changePassword($form->values->password);
		$user->clearHash();

		$this->em->flush();

		$this->user->login($user);
		$this->flashMessage('Your password has been successfully changed.');
		$this->redirect('User:');
	}

}
