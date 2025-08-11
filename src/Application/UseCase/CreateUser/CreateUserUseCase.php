<?php

namespace App\Application\UseCase\CreateUser;

use App\Domain\User\UserRepositoryInterface;
use InvalidArgumentException;

class CreateUserUseCase
{
  public function __construct(
    private readonly UserRepositoryInterface $userRepository
  )
  {
  }

  public function execute(CreateUserCommand $command): int
  {
    // Basic Validation
    if (!in_array($command->role, ['borrower', 'investor']))
    {
      throw new InvalidArgumentException("Invalid role specified. Must be 'borrower' or 'investor'.");
    }
    if (!filter_var($command->email, FILTER_VALIDATE_EMAIL))
    {
      throw new InvalidArgumentException("Invalid email format provided.");
    }

    return $this->userRepository->createCustomerUser($command);
  }
}