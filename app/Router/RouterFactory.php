<?php

namespace Presidos\Router;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

/**
 * RouterFactory
 *
 * @author Jan Marek
 */
class RouterFactory
{

	/**
	 * @return RouteList
	 */
	public static function createRouter()
	{
		$router = new RouteList();

		$router[] = new Route('user-settings', [
			'module' => 'User',
			'presenter' => 'User',
			'action' => 'default',
		]);

		$router[] = new Route('user-logout', [
			'module' => 'User',
			'presenter' => 'Login',
			'action' => 'logout',
		]);

		$router[] = new Route('user-<presenter>[/<action>][/<id [0-9]+>]', [
			'module' => 'User',
			'presenter' => NULL,
			'action' => 'default',
			'id' => NULL,
		]);

		$router[] = new Route('profile/<id [0-9]+>', [
			'module' => 'Presentation',
			'presenter' => 'Profile',
			'action' => 'default',
		]);

		$router[] = new Route('create-presentation', [
			'module' => 'Presentation',
			'presenter' => 'List',
			'action' => 'create',
		]);

		$router[] = new Route('presentation-<presenter>[/<action>][/<id [0-9]+>]', [
			'module' => 'Presentation',
			'presenter' => NULL,
			'action' => 'default',
			'id' => NULL,
		]);

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}
}
