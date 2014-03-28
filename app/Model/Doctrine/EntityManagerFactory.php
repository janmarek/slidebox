<?php

namespace Presidos\Model\Doctrine;

use Doctrine\Common\Cache\ArrayCache;
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

		$em = EntityManager::create($this->dbParams, $configuration);

		if ($this->devMode) {
			Panel::register($em->getConnection());
		}

		return $em;
	}

}