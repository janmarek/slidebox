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

class ProfilePresenter extends BasePresenter
{

	/** @var EntityManager */
	private $em;

	/** @var UserRepository */
	private $userRepository;

	public function __construct(EntityManager $em, UserRepository $userRepository)
	{
		$this->em = $em;
		$this->userRepository = $userRepository;
	}

	public function renderEditProfile()
	{
		$this->checkLoggedIn();
	}

	protected function createComponentEditProfileForm()
	{
		$user = $this->user->getIdentity();

		$form = new Form();

		if ($user->hasPassword()) {
			$form->addPassword('oldPassword', 'Fill your password:');
		}

		$form->addText('name', 'Name:')
			->setRequired('Please fill your name.');
		$form->addText('email', 'Email:')
			->setRequired('Please fill your password.');

		$form->addPassword('newPassword', 'Choose a new password:')
			->setOption('description', 'Keep this field blank if you don\'t want to change your password.');
		$form->addPassword('newPassword2', 'New password one more time:')
			->setOption('description', 'Fill your new password one more time if you want to change it.')
			->addConditionOn($form['newPassword'], Form::FILLED)
			->addRule(Form::FILLED, 'Please fill your password one more time.');

		$form->setDefaults(array(
			'name' => $user->getName(),
			'email' => $user->getEmail(),
		));

		$form->addSubmit('s', 'Save')
			->getControlPrototype()->class('btn btn-primary');

		$form->onSuccess[] = $this->submitEditProfileForm;

		return $form;
	}

	public function submitEditProfileForm(Form $form)
	{
		/** @var User $user */
		$user = $this->user->getIdentity();
		$values = $form->getValues();

		if ($user->hasPassword() && !$user->checkPassword($values->oldPassword)) {
			$form->addError('Please insert your password correctly.');
			return;
		}

		$user->setName($values->name);
		$user->setEmail($values->email);

		if ($values->newPassword) {
			$user->changePassword($values->newPassword);
		}

		$this->em->flush();

		$this->flashMessage('User settings has been successfully saved.');
		$this->redirect('User:');
	}

}
