<?php
namespace App\Domain\Loan;

interface InvestmentRepositoryInterface
{
  public function save(Investment $investment): void;
  public function getTotalInvestedAmount(int $loanId): float;
  public function findByLoanId(int $loanId): array;
}