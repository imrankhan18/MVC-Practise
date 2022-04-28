<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config\ConfigFactory;
use Phalcon\Escaper;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/components/',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);

$container->set(
    'config',
    function () {
        $fileName = '../app/etc/Config.php';
        $factory  = new ConfigFactory();
        $config = $factory->newInstance('php', $fileName);
        return $config;
    }
);
$container->set(
    'escaper',
    function () {
        $escaper = new Escaper();
        return $escaper;
    }
);
$eventsManager = new EventsManager();
$eventsManager->attach(
    'listener',
    new Listener()
);

$application->setEventsManager($eventsManager);
$eventsManager->attach(
    'application:beforeHandleRequest',
    new Listener()
);

$container->set(
    'eventsManager',
    function () use ($eventsManager) {
        return $eventsManager;
    }
);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
