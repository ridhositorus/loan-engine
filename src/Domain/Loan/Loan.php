<?php

namespace App\Domain\Loan;

use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;

class Loan
{
  private LoanStatus $status;
  private ?string $agreementLetterLink = null;
  private ?int $validatorEmployeeId = null;
  private ?\DateTimeImmutable $approvalDate = null;
  private ?string $proofPicture = null;
  private ?int $officerEmployeeId = null;
  private ?\DateTimeImmutable $disbursementDate = null;
  private ?string $signedAgreementLetter = null;
  private array $investments = [];

  public function __construct(
    private ?int           $id,
    private readonly int   $borrowerId,
    private readonly float $principalAmount,
    private readonly float $rate,
    private readonly float $roi,
    string                 $status = 'proposed'
  )
  {
    $this->status = LoanStatus::from($status);
  }

  public function approve(int $validatorId, string $proofPictureBase64): void
  {
    if ($this->status !== LoanStatus::PROPOSED)
    {
      throw new InvalidLoanStateTransitionException("Cannot approve a loan that is not in 'proposed' state.");
    }
    $this->status = LoanStatus::APPROVED;
    $this->validatorEmployeeId = $validatorId;
    $this->proofPicture = $proofPictureBase64;
    $this->approvalDate = new \DateTimeImmutable();
    $this->agreementLetterLink = "/agreements/loan_{$this->id}_" . uniqid('', true) . ".pdf";
  }

  public function addInvestment(float $amount, float $currentTotalInvested): void
  {
    if ($this->status !== LoanStatus::APPROVED)
    {
      throw new InvalidLoanStateTransitionException("Cannot invest in a loan that is not in 'approved' state.");
    }
    $totalInvested = $currentTotalInvested + $amount;
    if ($totalInvested > $this->principalAmount)
    {
      $remaining = $this->principalAmount - $currentTotalInvested;
      throw new \DomainException("Investment amount exceeds principal. Remaining amount: {$remaining}");
    }
    if ($totalInvested === $this->principalAmount)
    {
      $this->status = LoanStatus::INVESTED;
    }
  }

  /**
   * Transitions the loan to the 'disbursed' state.
   * @throws InvalidLoanStateTransitionException if the current state is not 'invested'.
   */
  public function disburse(int $officerId, string $signedAgreementBase64): void
  {
    if ($this->status !== LoanStatus::INVESTED)
    {
      throw new InvalidLoanStateTransitionException("Cannot disburse a loan that is not in 'invested' state.");
    }
    $this->status = LoanStatus::DISBURSED;
    $this->officerEmployeeId = $officerId;
    $this->signedAgreementLetter = $signedAgreementBase64;
    $this->disbursementDate = new \DateTimeImmutable();
  }

  // --- Setters for internal state management ---
  public function setId(int $id): void
  {
    $this->id = $id;
  } // Used after a new loan is created.

  public function setInvestments(array $investments): void
  {
    $this->investments = $investments;
  }

  // --- Getters to expose state to other layers ---
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getBorrowerId(): int
  {
    return $this->borrowerId;
  }

  public function getPrincipalAmount(): float
  {
    return $this->principalAmount;
  }

  public function getRate(): float
  {
    return $this->rate;
  }

  public function getRoi(): float
  {
    return $this->roi;
  }

  public function getStatus(): string
  {
    return $this->status->value;
  }

  public function getAgreementLetterLink(): ?string
  {
    return $this->agreementLetterLink;
  }

  public function getValidatorEmployeeId(): ?int
  {
    return $this->validatorEmployeeId;
  }

  public function getApprovalDate(): ?\DateTimeImmutable
  {
    return $this->approvalDate;
  }

  public function getProofPicture(): ?string
  {
    return $this->proofPicture;
  }

  public function getOfficerEmployeeId(): ?int
  {
    return $this->officerEmployeeId;
  }

  public function getDisbursementDate(): ?\DateTimeImmutable
  {
    return $this->disbursementDate;
  }

  public function getSignedAgreementLetter(): ?string
  {
    return $this->signedAgreementLetter;
  }

  public function getInvestments(): array
  {
    return $this->investments;
  }

  public function setAgreementLetterLink(?string $agreementLetterLink): Loan
  {
    $this->agreementLetterLink = $agreementLetterLink;
    return $this;
  }
}