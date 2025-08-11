<?php
namespace App\Application\UseCase\InvestInLoan;

use App\Domain\Loan\Investment;
use App\Domain\Loan\InvestmentRepositoryInterface;
use App\Domain\Loan\LoanRepositoryInterface;
use App\Domain\Shared\NotificationServiceInterface;

class InvestInLoanUseCase
{
  public function __construct(
    private readonly LoanRepositoryInterface $loanRepository,
    private readonly InvestmentRepositoryInterface $investmentRepository,
    private readonly NotificationServiceInterface $notificationService
  ) {}

  public function execute(InvestInLoanCommand $command): array
  {
    $loan = $this->loanRepository->findAndLockById($command->loanId);
    if (!$loan) {
      throw new \DomainException("Loan with ID {$command->loanId} not found.");
    }

    $currentTotal = $this->investmentRepository->getTotalInvestedAmount($command->loanId);

    $loan->addInvestment($command->amount, $currentTotal);

    $investment = new Investment($command->loanId, $command->investorId, $command->amount);
    $this->investmentRepository->save($investment);
    $this->loanRepository->save($loan);
    $isFullyFunded = ($loan->getStatus() === 'invested');
    if ($isFullyFunded) {
      $this->notificationService->notifyInvestors($loan->getId(), $loan->getAgreementLetterLink());
    }

    return [
      'new_total_invested' => $currentTotal + $command->amount,
      'is_fully_funded' => $isFullyFunded,
    ];
  }
}