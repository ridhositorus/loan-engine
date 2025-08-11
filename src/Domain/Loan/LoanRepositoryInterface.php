<?php
namespace App\Domain\Loan;

interface LoanRepositoryInterface
{
  public function findById(int $id): ?Loan;
  public function findAndLockById(int $id): ?Loan;
  public function save(Loan $loan): int; // Returns the loan ID
}