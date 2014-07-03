<?php

namespace SlideBox\User\Presenter;

use Doctrine\ORM\EntityManager;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Security\AuthenticationException;
use SlideBox\Presenter\BasePresenter;
use SlideBox\User\DuplicateEmailException;
use SlideBox\User\Email\ForgottenEmailFactory;
use SlideBox\User\Email\RegisterEmailFactory;
use SlideBox\User\FacebookAuthenticator;
use SlideBox\User\PasswordAuthenticator;
use SlideBox\User\User;
use SlideBox\User\UserRepository;

class FacebookLoginPresenter extends BasePresenter
{

	/** @var \Facebook */
	private $facebook;

	/** @var FacebookAuthenticator */
	private $facebookAuthenticator;

	public function __construct(\Facebook $facebook, FacebookAuthenticator $facebookAuthenticator)
	{
		$this->facebook = $facebook;
		$this->facebookAuthenticator = $facebookAuthenticator;
	}

	public function actionDefault($backlink = NULL)
	{
		$loginUrl = $this->facebook->getLoginUrl(array(
			'scope' => 'email',
			'redirect_uri' => $this->link('//facebookLogin', ['backlink' => $backlink]),
		));

		$this->redirectUrl($loginUrl);
	}

	public function actionFacebookLogin($backlink = NULL)
	{
		$me = $this->facebook->api('/me');
		try {
			$identity = $this->facebookAuthenticator->authenticate($me);
			$this->getUser()->login($identity);

			$this->flashMessage('You have been successfully logged in.');
			$this->restoreRequest($backlink);
			$this->redirect(':Presentation:List:');
		} catch (DuplicateEmailException $e) {
			$this->flashMessage(
				'User with email ' . $me['email'] . ' is already registered. ' .
				'You can login via password and then connect account with Facebook.'
			);
			$this->redirect(':Homepage:');
		}
	}

}
