<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\CreateLoan\CreateLoanCommand;
use App\Application\UseCase\CreateLoan\CreateLoanUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateLoanAction
{
  public function __construct(private readonly CreateLoanUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response): Response
  {
    $data = $request->getParsedBody();

    try
    {
      $command = new CreateLoanCommand(
        borrowerId: $data['borrower_id'],
        principalAmount: (float)$data['principal_amount'],
        rate: (float)$data['rate'],
        roi: (float)$data['roi']
      );

      $loanId = $this->useCase->execute($command);

      $result = ['message' => 'Loan created successfully', 'loan_id' => $loanId];
      $response->getBody()->write(json_encode($result, JSON_THROW_ON_ERROR));
      return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

    }
    catch (\InvalidArgumentException|\TypeError $e)
    {
      $response->getBody()->write(json_encode(['error' => 'Invalid input data. ' . $e->getMessage()]));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
  }
}