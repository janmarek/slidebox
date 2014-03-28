<?php

namespace Presidos\User\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Presidos\Presenter\BasePresenter;
use Presidos\User\Email\RegisterEmailFactory;
use Presidos\User\User;
use Presidos\User\UserRepository;

class RegisterPresenter extends BasePresenter
{

	/** @var EntityManager */
	private $em;

	/** @var UserRepository */
	private $userRepository;

	/** @var RegisterEmailFactory */
	private $registerEmailFactory;

	/** @var IMailer */
	private $mailer;

	public function __construct(EntityManager $em, UserRepository $userRepository,
		RegisterEmailFactory $registerEmailFactory, IMailer $mailer)
	{
		$this->em = $em;
		$this->userRepository = $userRepository;
		$this->registerEmailFactory = $registerEmailFactory;
		$this->mailer = $mailer;
	}

	public function actionDefault()
	{
	}

	protected function createComponentRegistrationForm()
	{
		$form = new Form();

		$form->addText('name', 'Name:')
			->setRequired('Please fill your name.')
			->getControlPrototype()->class('form-control');
		$form->addText('email', 'Email:')
			->setRequired('Please fill your e-mail.')
			->addRule(Form::EMAIL, 'Please fill your email correctly.')
			->getControlPrototype()->class('form-control');
		$form->addPassword('password', 'Password:')
			->setRequired('Please fill password.')
			->getControlPrototype()->class('form-control');
		$form->addPassword('password2', 'Password one more time:')
			->setRequired('Please insert password one more time.')
			->addRule(Form::EQUAL, 'Passwords has to be equal.', $form['password'])
			->getControlPrototype()->class('form-control');

		$form->addSubmit('s', 'Register')
			->getControlPrototype()->class('btn btn-primary');

		$form->onSuccess[] = array($this, 'registrationFormSubmitted');

		return $form;
	}

	public function registrationFormSubmitted(Form $form)
	{
		$values = $form->getValues();

		$user = new User();
		$user->setEmail($values->email);
		$user->setName($values->name);
		$user->changePassword($values->password);

		if (!$this->userRepository->hasUniqueEmail($user)) {
			$form->addError(
				'Email ' . $user->getEmail() . ' is already used by another user.'
			);
			return;
		}
		
		if (!$this->userRepository->hasUniqueUsername($user)) {
			$form->addError(
				'Name ' . $user->getName() . ' is already used by another user.'
			);
			return;
		}

		$this->em->persist($user);
		$this->em->flush();

		$message = $this->registerEmailFactory->create($this, $user);
		$this->mailer->send($message);

		$this->redirect('complete');
	}

	public function renderAllow($email, $hash)
	{
		$user = $this->userRepository->findByEmail($email, $hash);
		$this->checkExistence($user);
		$this->checkExistence($hash);

		if ($user->getHash() === $hash) {
			$user->allow();
			$this->em->flush();

			$this->user->login($user);
			$this->flashMessage('Uživatel byl úspěšně povolen.');
		} elseif ($user->isAllowed()) {
			$this->flashMessage('Uživatele je již povolený.');
		} else {
			$this->flashMessage('Uživatele se nepodařilo povolit.', 'danger');
			$this->redirect(':Homepage:');
		}

		$this->redirect('User:');
	}

}
