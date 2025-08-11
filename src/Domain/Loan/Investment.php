<?php

namespace App\Domain\Loan;

class Investment
{
  public function __construct(
    private readonly int   $loanId,
    private readonly int   $investorId,
    private readonly float $amount,
    private ?int           $id = null
  )
  {
  }

  public function getLoanId(): int
  {
    return $this->loanId;
  }

  public function getInvestorId(): int
  {
    return $this->investorId;
  }

  public function getAmount(): float
  {
    return $this->amount;
  }
}