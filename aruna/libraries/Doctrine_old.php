<?php

use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

// use Doctrine\DBAL\Logging\EchoSQLLogger;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

include APPPATH.'libraries/vendor/autoload.php';

class ARUNA_Doctrine {

  public $em = null;

  public function __construct()
  {
    // load database configuration from CodeIgniter
    require_once BASEPATH.'config/database.php';

    // Set up caches
    $queryCache = new PhpFilesAdapter('doctrine_queries');
    $metadataCache = new PhpFilesAdapter('doctrine_metadata');

    $config = new Configuration;
    $config->setMetadataCache($metadataCache);

    $isDevMode = true;
    $proxyDir = null;
    $cache = null;
    $useSimpleAnnotationReader = false;
    // $driverImpl = ORMSetup::createDefaultAnnotationDriver(array(APPPATH.'models/Entities'));
    $driverImpl = ORMSetup::createAnnotationMetadataConfiguration(array(APPPATH.'models/Entities'), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
    //$config->setMetadataDriverImpl($driverImpl);
    // $config->setQueryCache($queryCache);

    // // Proxy configuration
    // $config->setProxyDir(APPPATH.'/models/Proxies');
    // $config->setProxyNamespace('Proxies');
    // $config->setAutoGenerateProxyClasses(TRUE);

    // // Set up logger
    // $logger = new EchoSQLLogger;
    // $config->setSQLLogger($logger);

    // Database connection information
    $connectionOptions = array(
        'driver'    => 'pdo_mysql',
        'user'      => $db['default']['user'],
        'password'  => $db['default']['password'],
        'host'      => $db['default']['host'],
        'dbname'    => $db['default']['dbname']
    );

    // Create EntityManager
    $this->em = EntityManager::create($connectionOptions, $config);
  }
}