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

class UserPresenter extends BasePresenter
{

	/** @persistent */
	public $backlink = '';

	/** @var \Facebook */
	private $facebook;

	/** @var FacebookAuthenticator */
	private $facebookAuthenticator;

	/** @var PasswordAuthenticator */
	private $passwordAuthenticator;

	/** @var EntityManager */
	private $em;

	/** @var UserRepository */
	private $userRepository;

	/** @var RegisterEmailFactory */
	private $registerEmailFactory;

	/** @var ForgottenEmailFactory */
	private $forgottenEmailFactory;

	/** @var IMailer */
	private $mailer;

	public function __construct(\Facebook $facebook, FacebookAuthenticator $facebookAuthenticator,
		PasswordAuthenticator $passwordAuthenticator, EntityManager $em, UserRepository $userRepository,
		RegisterEmailFactory $registerEmailFactory, ForgottenEmailFactory $forgottenEmailFactory, IMailer $mailer)
	{
		$this->facebook = $facebook;
		$this->facebookAuthenticator = $facebookAuthenticator;
		$this->passwordAuthenticator = $passwordAuthenticator;
		$this->em = $em;
		$this->userRepository = $userRepository;
		$this->registerEmailFactory = $registerEmailFactory;
		$this->forgottenEmailFactory = $forgottenEmailFactory;
		$this->mailer = $mailer;
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
		$this->em->flush();

		$this->flashMessage('Your account has been successfully connected with facebook.');
		$this->redirect('default');
	}

}
