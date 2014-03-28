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
		$identity = $this->facebookAuthenticator->authenticate($me);
		$this->getUser()->login($identity);

		$this->flashMessage('You have been successfully logged in.');
		$this->restoreRequest($backlink);
		$this->redirect('User:');
	}

}
