<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once APPPATH.'libraries/vendor/autoload.php';
// require_once APPPATH.'models/Entities/Accounts.php';

class ARUNA_Doctrine {

  public $em = null;

  public function __construct()
  {
    // load database configuration from CodeIgniter
    require_once BASEPATH.'config/database.php';

    $isDevMode = true;
    $proxyDir = null;
    $cache = null;
    $useSimpleAnnotationReader = false;

    // $config = new Configuration;
    // $config->addEntityNamespace('', 'models/Entities');
    $config = ORMSetup::createAnnotationMetadataConfiguration(array(APPPATH.'models/Entities'), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

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