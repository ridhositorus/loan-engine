<?php

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Loan\Investment;
use App\Domain\Loan\InvestmentRepositoryInterface;
use PDO;

class InvestmentRepository implements InvestmentRepositoryInterface
{
  public function __construct(private readonly PDO $pdo)
  {
  }

  public function save(Investment $investment): void
  {
    $sql = "INSERT INTO investments (loan_id, investor_id, amount) VALUES (?, ?, ?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $investment->getLoanId(),
      $investment->getInvestorId(),
      $investment->getAmount(),
    ]);
  }

  public function getTotalInvestedAmount(int $loanId): float
  {
    $sql = "SELECT SUM(amount) FROM investments WHERE loan_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$loanId]);
    return (float)($stmt->fetchColumn() ?? 0.0);
  }

  public function findByLoanId(int $loanId): array
  {
    $sql = "SELECT * FROM investments WHERE loan_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$loanId]);

    $investments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $investments[] = new Investment(
        loanId: $row['loan_id'],
        investorId: $row['investor_id'],
        amount: (float)$row['amount'],
        id: $row['id']
      );
    }

    return $investments;
  }
}