<?php
namespace App\Application\UseCase\ApproveLoan;

class ApproveLoanCommand
{
  public function __construct(
    public readonly int $loanId,
    public readonly int $validatorEmployeeId,
    public readonly string $proofPicture
  ) {}
}