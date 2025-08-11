<?php
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
  $containerBuilder->addDefinitions([
    'settings' => [
      'displayErrorDetails' => $_ENV['APP_DEBUG'] === 'true',
      'db' => [
        'host' => $_ENV['DB_HOST'],
        'dbname' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASSWORD'],
      ]
    ]
  ]);
};