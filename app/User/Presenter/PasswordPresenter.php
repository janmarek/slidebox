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
			->setRequired('Vyplňte prosím váš e-mail, pod kterým jste registrováni.')
			->addRule(Form::EMAIL, 'Vyplňte prosím e-mail správně.')
			->getControlPrototype()->class('form-control');
		$form->addSubmit('s', 'Získat nové heslo')
			->getControlPrototype()->class('btn btn-blue');
		$form->onSuccess[] = $this->submitSendNewPasswordForm;

		return $form;
	}

	public function submitSendNewPasswordForm(Form $form)
	{
		$user = $this->userRepository->findAllowedByEmail($form->values->email);

		if (!$user) {
			$form->addError('Uživatel s mailem ' . $form->values->email . ' není v systému registrován.');
			return;
		}

		$user->changeHash();

		$this->em->flush();

		$message = $this->forgottenEmailFactory->create($this, $user);
		$this->mailer->send($message);

		$this->flashMessage('E-mail s instrukcemi pro zadání nového hesla byl úspěšně zaslán.');
		$this->redirect(':Homepage:');
	}

	public function renderNew($email, $hash)
	{
		$user = $this->userRepository->findByEmailAndHash($email, $hash);

		if (!$user) {
			$this->flashMessage('Uživatel nebyl nalezen nebo heslo již bylo změněno.', 'error');
			$this->redirect(':Homepage:');
		}
	}

	protected function createComponentNewPasswordForm()
	{
		$form = new Form();
		$form->addPassword('password', 'Nové heslo:')
			->setRequired('Zadejte prosím heslo.');
		$form->addPassword('password2', 'Heslo ještě jedno:')
			->setRequired('Zadejte heslo pro kontrolu ještě jednou.')
			->addRule(Form::EQUAL, 'Hesla se musí shodovat.', $form['password']);
		$form->addSubmit('s', 'Změnit heslo')
			->getControlPrototype()->class('btn btn-blue');
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
		$this->flashMessage('Heslo bylo úspěšně změněno.');
		$this->redirect('User:');
	}

}
