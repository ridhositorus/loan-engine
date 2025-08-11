<?php

namespace App\Infrastructure\Http\Action;

use App\Application\UseCase\DisburseLoan\DisburseLoanCommand;
use App\Application\UseCase\DisburseLoan\DisburseLoanUseCase;
use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DisburseLoanAction
{
  public function __construct(private readonly DisburseLoanUseCase $useCase)
  {
  }

  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $data = $request->getParsedBody();
    $loanId = (int)$args['id'];

    try
    {
      $command = new DisburseLoanCommand(
        loanId: $loanId,
        officerEmployeeId: $data['officer_employee_id'],
        signedAgreementLetter: $data['signed_agreement_letter']
      );

      $this->useCase->execute($command);

      $result = ['message' => 'Loan disbursed successfully'];
      $response->getBody()->write(json_encode($result));
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

    }
    catch (InvalidLoanStateTransitionException $e)
    {
      $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
      return $response->withStatus(409)->withHeader('Content-Type', 'application/json'); // 409 Conflict
    }
    catch (\InvalidArgumentException|\DomainException $e)
    {
      $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
  }
}