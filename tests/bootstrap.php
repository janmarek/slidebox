<?php

use Presidos\User\DummyUserStorage;

define('CANCEL_START_APP', TRUE);
define('TEST_MODE', TRUE);

require __DIR__ . '/../www/index.php';
require __DIR__ . '/BaseTestCase.php';
require __DIR__ . '/IntegrationTestCase.php';

Tester\Environment::setup();

$container->removeService('nette.userStorage');
$container->addService('nette.userStorage', new DummyUserStorage());