<?php
namespace App\Application\UseCase\CreateLoan;

use App\Domain\Loan\Loan;
use App\Domain\Loan\LoanRepositoryInterface;
use App\Domain\Loan\LoanStatus;

class CreateLoanUseCase
{
  public function __construct(
    private readonly LoanRepositoryInterface $loanRepository
  ) {}

  public function execute(CreateLoanCommand $command): int
  {
    $loan = new Loan(
      id: null, // ID is null on creation
      borrowerId: $command->borrowerId,
      principalAmount: $command->principalAmount,
      rate: $command->rate,
      roi: $command->roi,
      status: 'proposed'
    );

    return $this->loanRepository->save($loan);
  }
}