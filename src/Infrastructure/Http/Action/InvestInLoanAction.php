<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\InvestInLoan\InvestInLoanCommand;
use App\Application\UseCase\InvestInLoan\InvestInLoanUseCase;
use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InvestInLoanAction
{
  public function __construct(private readonly InvestInLoanUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $data = $request->getParsedBody();
    $loanId = (int)$args['id'];

    try
    {
      $command = new InvestInLoanCommand(
        loanId: $loanId,
        investorId: $data['investor_id'],
        amount: (float)$data['amount']
      );

      $result = $this->useCase->execute($command);

      $message = $result['is_fully_funded']
        ? 'Investment successful. Loan is now fully funded.'
        : 'Investment successful.';

      $responseBody = [
        'message' => $message,
        'new_total_invested' => $result['new_total_invested'],
      ];

      $response->getBody()->write(json_encode($responseBody, JSON_THROW_ON_ERROR));
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
    catch (InvalidLoanStateTransitionException $e)
    {
      $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR));
      return $response->withStatus(409)->withHeader('Content-Type', 'application/json'); // 409 Conflict for invalid state
    }
    catch (\DomainException $e)
    {
      // Catches specific domain errors like 'exceeds principal'
      $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
  }
}