<?php
namespace App\Application\UseCase\ApproveLoan;

use App\Domain\Loan\LoanRepositoryInterface;
use App\Validation\CustomValidation;

class ApproveLoanUseCase
{
  public function __construct(
    private readonly LoanRepositoryInterface $loanRepository
  ) {}

  public function execute(ApproveLoanCommand $command): string
  {
    if (!CustomValidation::isBase64($command->proofPicture)) {
      throw new \InvalidArgumentException('Proof picture must be a valid base64 string.');
    }

    $loan = $this->loanRepository->findAndLockById($command->loanId);

    if (!$loan) {
      throw new \DomainException("Loan with ID {$command->loanId} not found.");
    }

    $loan->approve($command->validatorEmployeeId, $command->proofPicture);

    $this->loanRepository->save($loan);

    return $loan->getAgreementLetterLink();
  }
}