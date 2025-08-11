<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\CreateUser\CreateUserCommand;
use App\Application\UseCase\CreateUser\CreateUserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDOException;

class CreateUserAction
{
  public function __construct(private readonly CreateUserUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response): Response
  {
    $data = $request->getParsedBody();

    try
    {
      $command = new CreateUserCommand(
        idNumber: $data['id_number'],
        name: $data['name'],
        email: $data['email'],
        role: $data['role']
      );

      $userId = $this->useCase->execute($command);

      $result = ['message' => 'User created successfully', 'user_id' => $userId];
      $response->getBody()->write(json_encode($result));
      return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

    }
    catch (PDOException $e)
    {
      if ($e->errorInfo[1] == 1062)
      { // 1062 is the MySQL error code for duplicate entry
        $response->getBody()->write(json_encode(['error' => 'User with this email or ID number already exists.'], JSON_THROW_ON_ERROR));
        return $response->withStatus(409)->withHeader('Content-Type', 'application/json'); // 409 Conflict
      }
      // For other database errors
      $response->getBody()->write(json_encode(['error' => 'A database error occurred.'], JSON_THROW_ON_ERROR));
      return $response->withStatus(500)->withHeader('Content-Type', 'application/json');

    }
    catch (\InvalidArgumentException $e)
    {
      // Catches validation errors from the Use Case
      $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
  }
}