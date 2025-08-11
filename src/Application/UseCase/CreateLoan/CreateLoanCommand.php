<?php
namespace App\Application\UseCase\CreateLoan;

class CreateLoanCommand
{
  public function __construct(
    public readonly int $borrowerId,
    public readonly float $principalAmount,
    public readonly float $rate,
    public readonly float $roi
  ) {}
}