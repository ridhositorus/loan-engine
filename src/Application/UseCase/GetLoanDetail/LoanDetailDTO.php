<?php

namespace App\Application\UseCase\GetLoanDetail;

use App\Domain\Loan\Loan;

class LoanDetailDTO
{
  public readonly array $investments;

  private function __construct(
    public readonly int     $id,
    public readonly int     $borrowerId,
    public readonly float   $principalAmount,
    public readonly string  $status,
    public readonly ?string $agreementLetterLink,
    array                   $investmentsData
  )
  {
    $this->investments = array_map(static fn($inv) => [
      'investor_id' => $inv->getInvestorId(),
      'amount' => $inv->getAmount(),
    ], $investmentsData);
  }

  public static function fromEntity(Loan $loan): self
  {
    return new self(
      id: $loan->getId(),
      borrowerId: $loan->getBorrowerId(),
      principalAmount: $loan->getPrincipalAmount(),
      status: $loan->getStatus(),
      agreementLetterLink: $loan->getAgreementLetterLink(),
      investmentsData: $loan->getInvestments()
    );
  }
}