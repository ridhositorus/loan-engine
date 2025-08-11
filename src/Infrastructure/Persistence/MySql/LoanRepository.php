<?php

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Loan\Loan;
use App\Domain\Loan\LoanRepositoryInterface;
use PDO;

class LoanRepository implements LoanRepositoryInterface
{
  public function __construct(private readonly PDO $pdo)
  {
  }

  /**
   * Finds a loan by its ID without locking it.
   */
  public function findById(int $id): ?Loan
  {
    $stmt = $this->pdo->prepare("SELECT * FROM loans WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data ? $this->fillFromArray($data) : null;
  }

  /**
   * Finds a loan by its ID and locks the database row for update
   * to prevent race conditions during state transitions.
   */
  public function findAndLockById(int $id): ?Loan
  {
    if (!$this->pdo->inTransaction())
    {
      $this->pdo->beginTransaction();
    }
    $stmt = $this->pdo->prepare("SELECT * FROM loans WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data ? $this->fillFromArray($data) : null;
  }

  /**
   * Saves a Loan entity to the database.
   * It handles both creating a new loan (INSERT) and updating an existing one (UPDATE).
   * @return int The ID of the saved loan.
   */
  public function save(Loan $loan): int
  {
    if ($loan->getId() === null)
    {
      // This is a new loan, so we perform an INSERT.
      $sql = "INSERT INTO loans (borrower_id, principal_amount, rate, roi, status) 
                    VALUES (:borrower_id, :principal_amount, :rate, :roi, :status)";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
        ':borrower_id' => $loan->getBorrowerId(),
        ':principal_amount' => $loan->getPrincipalAmount(),
        ':rate' => $loan->getRate(),
        ':roi' => $loan->getRoi(),
        ':status' => $loan->getStatus(),
      ]);
      $id = (int)$this->pdo->lastInsertId();
      $loan->setId($id); // Set the new ID back on the entity object.
    }
    else
    {
      // This is an existing loan, so we perform an UPDATE.
      $sql = "UPDATE loans SET 
                        status = :status, 
                        agreement_letter_link = :agreement_link,
                        validator_employee_id = :validator_id,
                        approval_date = :approval_date,
                        proof_picture = :proof_picture,
                        officer_employee_id = :officer_id,
                        disbursement_date = :disbursement_date,
                        signed_agreement_letter = :signed_agreement_letter
                    WHERE id = :id";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
        ':id' => $loan->getId(),
        ':status' => $loan->getStatus(),
        ':agreement_link' => $loan->getAgreementLetterLink(),
        ':validator_id' => $loan->getValidatorEmployeeId(),
        ':approval_date' => $loan->getApprovalDate()?->format('Y-m-d H:i:s'),
        ':proof_picture' => $loan->getProofPicture(),
        ':officer_id' => $loan->getOfficerEmployeeId(),
        ':disbursement_date' => $loan->getDisbursementDate()?->format('Y-m-d H:i:s'),
        ':signed_agreement_letter' => $loan->getSignedAgreementLetter(),
      ]);
      $id = $loan->getId();
    }

    // Commit the transaction if one was started (e.g., by findAndLockById)
    if ($this->pdo->inTransaction())
    {
      $this->pdo->commit();
    }

    return $id;
  }

  private function fillFromArray(array $data): Loan
  {
    return new Loan(
      id: (int)$data['id'],
      borrowerId: (int)$data['borrower_id'],
      principalAmount: (float)$data['principal_amount'],
      rate: (float)$data['rate'],
      roi: (float)$data['roi'],
      status: $data['status']
    );
  }
}