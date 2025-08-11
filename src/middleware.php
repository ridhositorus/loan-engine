<?php
use Slim\App;

return function (App $app) {
  $app->addBodyParsingMiddleware();
  $app->addErrorMiddleware(
    $_ENV['APP_DEBUG'] === 'true',
    true,
    true
  );
};