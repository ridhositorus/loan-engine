<?php
namespace App\Application\UseCase\GetLoanDetail;

use App\Domain\Loan\InvestmentRepositoryInterface;
use App\Domain\Loan\LoanRepositoryInterface;

class GetLoanDetailUseCase
{
  public function __construct(
    private readonly LoanRepositoryInterface $loanRepository,
    private readonly InvestmentRepositoryInterface $investmentRepository
  ) {}

  public function execute(int $loanId): ?LoanDetailDTO
  {
    $loan = $this->loanRepository->findById($loanId);

    if (!$loan) {
      return null;
    }

    // Hydrate the loan with its investments
    $investments = $this->investmentRepository->findByLoanId($loanId);
    $loan->setInvestments($investments);

    return LoanDetailDTO::fromEntity($loan);
  }
}