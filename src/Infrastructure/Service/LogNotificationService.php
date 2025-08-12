<?php

namespace App\Infrastructure\Service;

use App\Domain\Shared\NotificationServiceInterface;
use PDO;

/**
 * A simple implementation of the NotificationService that logs messages
 * to the standard error log instead of sending real emails.
 */
class LogNotificationService implements NotificationServiceInterface
{
  public function __construct(private readonly PDO $pdo)
  {
  }
  public function notifyInvestors(int $loanId, string $agreementLink): void
  {
    // SQL query to get the email addresses of all investors for a given loan.
    $sql = "SELECT u.email FROM customer_users u 
                JOIN investments i ON u.id = i.investor_id 
                WHERE i.loan_id = ?";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$loanId]);

    $investorEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($investorEmails))
    {
      $warningMessage = "[Notification Service] WARNING: No investors found for fully-funded Loan ID: {$loanId}.";
      // Send the warning to stderr as well
      error_log($warningMessage, 3, 'php://stderr');
      return;
    }

    foreach ($investorEmails as $email)
    {
      $warningMessage = sprintf(
        "[Notification Service] SIMULATING EMAIL to %s: Your investment in Loan ID %d is complete. Please find your agreement at: %s",
        $email,
        $loanId,
        $agreementLink
      );
      // error_log() sends the message to the container's log output.
      error_log($warningMessage, 3, 'php://stderr');
    }
  }
}