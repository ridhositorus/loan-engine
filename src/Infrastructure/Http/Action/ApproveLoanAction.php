<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\ApproveLoan\ApproveLoanCommand;
use App\Application\UseCase\ApproveLoan\ApproveLoanUseCase;
use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApproveLoanAction
{
  public function __construct(private readonly ApproveLoanUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $loanId = (int)$args['id'];
    $data = $request->getParsedBody();

    $command = new ApproveLoanCommand(
      $loanId,
      $data['validator_employee_id'],
      $data['proof_picture']
    );

    try
    {
      $agreementLink = $this->useCase->execute($command);

      $result = ['message' => 'Loan approved successfully', 'agreement_link' => $agreementLink];
      $response->getBody()->write(json_encode($result));
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
    catch (InvalidLoanStateTransitionException $e)
    {
      $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
      return $response->withStatus(409); // Conflict
    }
    catch (\DomainException|\InvalidArgumentException $e)
    {
      $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
      return $response->withStatus(400); // Bad Request
    }
  }
}