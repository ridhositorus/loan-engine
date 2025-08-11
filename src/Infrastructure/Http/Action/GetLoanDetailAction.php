<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\GetLoanDetail\GetLoanDetailUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetLoanDetailAction
{
  public function __construct(private readonly GetLoanDetailUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $loanId = (int)$args['id'];
    $loanDTO = $this->useCase->execute($loanId);

    if (!$loanDTO)
    {
      $response->getBody()->write(json_encode(['error' => 'Loan not found']));
      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($loanDTO));
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
  }
}