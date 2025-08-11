<?php
namespace App;

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\App as SlimApp;

class App
{
  public static function bootstrap(): SlimApp
  {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $containerBuilder = new ContainerBuilder();

    $settings = require __DIR__ . '/settings.php';
    $settings($containerBuilder);

    $dependencies = require __DIR__ . '/dependencies.php';
    $dependencies($containerBuilder);

    $container = $containerBuilder->build();

    AppFactory::setContainer($container);
    $app = AppFactory::create();

    $middleware = require __DIR__ . '/middleware.php';
    $middleware($app);

    $app->group('/api', function ($group) {
      $routes = require __DIR__ . '/Infrastructure/Http/routes.php';
      $routes($group);
    });

    $app->addRoutingMiddleware();

    return $app;
  }
}