<?php

namespace Presidos\View;
use Nette\Utils\Html;
use Nette\Utils\Strings;
use Presidos\User\User;

/**
 * @author Jan Marek
 */
class UserIconHelper
{

	public function __invoke(User $user = NULL)
	{
		if (!$user) {
			return;
		}

		$img = Html::el('img', ['width' => 50, 'height' => 50]);

		if ($user->getFacebookUid()) {
			$img->src('https://graph.facebook.com/' . $user->getFacebookUid() . '/picture?type=square');
		} else {
			$hash = md5(Strings::lower($user->getEmail()));
			$img->src('http://www.gravatar.com/avatar/' . $hash . '?s=50&d=mm');
		}

		return $img;
	}

} 