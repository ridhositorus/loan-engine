<?php
namespace App\Infrastructure\Persistence\MySql;

use App\Application\UseCase\CreateUser\CreateUserCommand;
use App\Domain\User\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
  public function __construct(private readonly PDO $pdo) {}

  public function createCustomerUser(CreateUserCommand $command): int
  {
    $sql = "INSERT INTO customer_users (id_number, name, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $command->idNumber,
      $command->name,
      $command->email,
      $command->role
    ]);

    return (int) $this->pdo->lastInsertId();
  }
}