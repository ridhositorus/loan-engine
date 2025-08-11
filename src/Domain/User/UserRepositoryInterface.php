<?php
namespace App\Domain\User;

use App\Application\UseCase\CreateUser\CreateUserCommand;

interface UserRepositoryInterface
{
  public function createCustomerUser(CreateUserCommand $command): int;
}