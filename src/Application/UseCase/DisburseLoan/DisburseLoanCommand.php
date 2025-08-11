<?php
namespace App\Application\UseCase\DisburseLoan;

class DisburseLoanCommand
{
  public function __construct(
    public readonly int $loanId,
    public readonly int $officerEmployeeId,
    public readonly string $signedAgreementLetter
  ) {}
}