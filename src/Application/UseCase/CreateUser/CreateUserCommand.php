<?php
namespace App\Application\UseCase\CreateUser;

class CreateUserCommand
{
  public function __construct(
    public readonly string $idNumber,
    public readonly string $name,
    public readonly string $email,
    public readonly string $role,
  ) {}
}