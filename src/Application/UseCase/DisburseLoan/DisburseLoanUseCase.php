<?php

namespace App\Application\UseCase\DisburseLoan;

use App\Domain\Loan\LoanRepositoryInterface;
use App\Validation\CustomValidation;

class DisburseLoanUseCase
{
  public function __construct(
    private readonly LoanRepositoryInterface $loanRepository
  )
  {
  }

  public function execute(DisburseLoanCommand $command): void
  {
    if (!CustomValidation::isBase64($command->signedAgreementLetter))
    {
      throw new \InvalidArgumentException('Signed agreement letter must be a valid base64 string.');
    }

    $loan = $this->loanRepository->findAndLockById($command->loanId);

    if (!$loan)
    {
      throw new \DomainException("Loan with ID {$command->loanId} not found.");
    }

    $loan->disburse($command->officerEmployeeId, $command->signedAgreementLetter);
    $this->loanRepository->save($loan);
  }
}