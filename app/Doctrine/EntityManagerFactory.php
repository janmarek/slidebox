<?php

namespace Presidos\Doctrine;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Kdyby\Doctrine\Diagnostics\Panel;
use Kdyby\DoctrineCache\Cache;
use Nette\Caching\IStorage;

/**
 * @author Jan Marek
 */
class EntityManagerFactory
{

	private $dbParams;

	private $devMode;

	private $entityDirs;

	private $cacheStorage;

	private $proxyDir;

	public function __construct($dbParams, $devMode, $entityDirs, $proxyDir, IStorage $cacheStorage)
	{
		$this->dbParams = $dbParams;
		$this->devMode = $devMode;
		$this->entityDirs = $entityDirs;
		$this->proxyDir = $proxyDir;
		$this->cacheStorage = $cacheStorage;
	}

	public function createEntityManager()
	{
		$cache = $this->devMode ? new ArrayCache() : new Cache($this->cacheStorage);

		$configuration = Setup::createAnnotationMetadataConfiguration(
			$this->entityDirs,
			$this->devMode,
			$this->proxyDir,
			$cache,
			FALSE
		);

		$evm = new EventManager();
		$evm->addEventSubscriber(new MysqlSessionInit('utf8', 'utf8_general_ci'));

		$em = EntityManager::create($this->dbParams, $configuration, $evm);

		if ($this->devMode) {
			Panel::register($em->getConnection());
		}

		return $em;
	}

}