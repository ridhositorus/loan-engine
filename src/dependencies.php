<?php

use App\Domain\Loan\InvestmentRepositoryInterface;
use App\Domain\Loan\LoanRepositoryInterface;
use App\Domain\Shared\NotificationServiceInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\MySql\InvestmentRepository;
use App\Infrastructure\Persistence\MySql\LoanRepository;
use App\Infrastructure\Persistence\MySql\UserRepository;
use App\Infrastructure\Service\LogNotificationService;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder)
{
  $containerBuilder->addDefinitions([
    PDO::class => function (ContainerInterface $c)
    {
      $settings = $c->get('settings')['db'];
      $dsn = "mysql:host={$settings['host']};dbname={$settings['dbname']};charset=utf8mb4";
      $pdo = new PDO($dsn, $settings['user'], $settings['pass']);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      return $pdo;
    },

    LoanRepositoryInterface::class => \DI\autowire(LoanRepository::class),
    InvestmentRepositoryInterface::class => \DI\autowire(InvestmentRepository::class),
    UserRepositoryInterface::class => \DI\autowire(UserRepository::class),

    NotificationServiceInterface::class => \DI\autowire(LogNotificationService::class),
  ]);

};