<?php
namespace App\Application\UseCase\InvestInLoan;

class InvestInLoanCommand
{
  public function __construct(
    public readonly int $loanId,
    public readonly int $investorId,
    public readonly float $amount
  ) {}
}