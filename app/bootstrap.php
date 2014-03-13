<?php

define('ROOT_DIR', __DIR__ . '/..');
define('WWW_DIR', ROOT_DIR . '/www');
define('CONFIG_DIR', ROOT_DIR . '/config');

require ROOT_DIR . '/libs/autoload.php';

$configurator = new Nette\Config\Configurator();

$debugMode = file_exists(CONFIG_DIR . '/dev');
$configurator->setDebugMode($debugMode);
$configurator->enableDebugger(ROOT_DIR . '/log');
$configurator->setTempDirectory(ROOT_DIR . '/temp');

$webloaderExtension = new \WebLoader\Nette\Extension();
$webloaderExtension->install($configurator);

$configurator->addConfig(CONFIG_DIR . '/config.neon', FALSE);
$configurator->addConfig(CONFIG_DIR . '/config.local.neon', FALSE);
$container = $configurator->createContainer();

$container->application->catchExceptions = !$debugMode;

if (!defined('CANCEL_START_APP')) {
	$container->application->run();
}