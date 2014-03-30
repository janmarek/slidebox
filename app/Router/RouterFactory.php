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
	 * @return \Nette\Application\Routers\RouteList
	 */
	public static function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
