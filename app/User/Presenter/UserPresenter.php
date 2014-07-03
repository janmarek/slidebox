<?php

namespace SlideBox\User\Presenter;

use Doctrine\ORM\EntityManager;
use Facebook;
use Nette\Application\UI\Form;
use SlideBox\Presenter\BasePresenter;
use SlideBox\User\User;
use SlideBox\User\UserRepository;

class UserPresenter extends BasePresenter
{

	/** @var Facebook */
	private $facebook;

	/** @var EntityManager */
	private $em;

	/** @var UserRepository */
	private $userRepository;

	public function __construct(Facebook $facebook, EntityManager $em, UserRepository $userRepository)
	{
		$this->facebook = $facebook;
		$this->em = $em;
		$this->userRepository = $userRepository;
	}

	public function actionDefault()
	{
		$this->checkLoggedIn();

		$facebookConnectUrl = $this->facebook->getLoginUrl(array(
			'scope' => 'email',
			'redirect_uri' => $this->link('//facebookConnect'),
		));
		$this->template->facebookConnectUrl = $facebookConnectUrl;
	}

	public function actionFacebookConnect()
	{
		$this->checkLoggedIn();

		$user = $this->getUser()->getIdentity();
		$user->setFacebookUid($this->facebook->getUser());

		if ($this->userRepository->hasUniqueFacebook($user)) {
			$this->em->flush();
			$this->flashMessage('Your account has been successfully connected with facebook.');
		} else {
			$this->flashMessage('Facebook user is already registered.', 'danger');
		}

		$this->redirect('default');
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
		$form->addTextArea('description', 'Description:');

		$form->addPassword('newPassword', 'Choose a new password:');
		$form->addPassword('newPassword2', 'New password one more time:')
			->addConditionOn($form['newPassword'], Form::FILLED)
			->addRule(Form::FILLED, 'Please fill your password one more time.')
			->addRule(Form::EQUAL, 'New passwords has to match', $form['newPassword']);

		$form->setDefaults(array(
			'name' => $user->getName(),
			'email' => $user->getEmail(),
			'description' => $user->getDescription(),
		));

		$form->addSubmit('s', 'Save')
			->getControlPrototype();

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
		$user->setDescription($values->description);

		if ($values->newPassword) {
			$user->changePassword($values->newPassword);
		}

		$this->em->flush();

		$this->flashMessage('User settings has been successfully saved.');
		$this->redirect('User:');
	}

}
