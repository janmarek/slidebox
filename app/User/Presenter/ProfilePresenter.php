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
			$form->addPassword('oldPassword', 'Zadejte své heslo:');
		}

		$form->addText('name', 'Jméno:')
			->setRequired('Zadejte prosím jméno.');
		$form->addText('email', 'E-mail:')
			->setRequired('Zadejte prosím heslo.');

		$form->addPassword('newPassword', 'Zvolte si nové heslo:')
			->setOption('description', 'Pokud nechcete heslo měnit, nechte toto pole volné.');
		$form->addPassword('newPassword2', 'Kontrola nového hesla:')
			->setOption('description', 'Pokud zadáváte nové heslo, napište ho pro kontrolu ještě jednou.')
			->addConditionOn($form['newPassword'], Form::FILLED)
			->addRule(Form::FILLED, 'Zadejte prosím kontrolu hesla.');

		$form->setDefaults(array(
			'name' => $user->getName(),
			'email' => $user->getEmail(),
		));

		$form->addSubmit('s', 'Uložit')
			->getControlPrototype()->class('btn btn-blue');

		$form->onSuccess[] = $this->submitEditProfileForm;

		return $form;
	}

	public function submitEditProfileForm(Form $form)
	{
		/** @var User $user */
		$user = $this->user->getIdentity();
		$values = $form->getValues();

		if ($user->hasPassword() && !$user->checkPassword($values->oldPassword)) {
			$form->addError('Zadejte správné heslo.');
			return;
		}

		$user->setName($values->name);
		$user->setEmail($values->email);

		if ($values->newPassword) {
			$user->changePassword($values->newPassword);
		}

		$this->em->flush();

		$this->flashMessage('Uživatelské údaje byly úspěšně nastaveny.');
		$this->redirect('User:');
	}

}
