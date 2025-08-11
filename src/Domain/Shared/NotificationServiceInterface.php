<?php
namespace App\Domain\Shared;

interface NotificationServiceInterface
{
  // email sending functionality is not implemented due to simplicity
  public function notifyInvestors(int $loanId, string $agreementLink): void;
}