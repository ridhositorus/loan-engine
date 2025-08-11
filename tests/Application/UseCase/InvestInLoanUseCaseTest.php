<?php
namespace Tests\Application\UseCase;

use App\Application\UseCase\InvestInLoan\{InvestInLoanCommand, InvestInLoanUseCase};
use App\Domain\Loan\{InvestmentRepositoryInterface, Loan, LoanRepositoryInterface};
use App\Domain\Shared\NotificationServiceInterface;
use PHPUnit\Framework\TestCase;

class InvestInLoanUseCaseTest extends TestCase
{
  private LoanRepositoryInterface $loanRepoMock;
  private InvestmentRepositoryInterface $investmentRepoMock;
  private NotificationServiceInterface $notificationMock;
  private InvestInLoanUseCase $useCase;

  protected function setUp(): void
  {
    $this->loanRepoMock = $this->createMock(LoanRepositoryInterface::class);
    $this->investmentRepoMock = $this->createMock(InvestmentRepositoryInterface::class);
    $this->notificationMock = $this->createMock(NotificationServiceInterface::class);

    $this->useCase = new InvestInLoanUseCase(
      $this->loanRepoMock,
      $this->investmentRepoMock,
      $this->notificationMock
    );
  }

  public function testShouldSuccessfullyInvestAndTriggerNotificationsWhenFullyFunded(): void
  {
    $loan = new Loan(1, 1, 10000, 10, 8, 'approved');
    $loan->setAgreementLetterLink('test-link');
    $this->loanRepoMock->method('findAndLockById')->willReturn($loan);
    $this->investmentRepoMock->method('getTotalInvestedAmount')->willReturn(5000.0);

    $this->notificationMock->expects($this->once())->method('notifyInvestors');

    $command = new InvestInLoanCommand(1, 101, 5000.0);

    $result = $this->useCase->execute($command);

    $this->assertEquals(10000.0, $result['new_total_invested']);
    $this->assertTrue($result['is_fully_funded']);
    $this->assertEquals('invested', $loan->getStatus());
  }

  public function testShouldThrowExceptionWhenInvestmentExceedsPrincipal(): void
  {
    $this->expectException(\DomainException::class);
    $this->expectExceptionMessage('Investment amount exceeds principal.');

    $loan = new Loan(1, 1, 10000, 10, 8, 'approved');
    $this->loanRepoMock->method('findAndLockById')->willReturn($loan);
    $this->investmentRepoMock->method('getTotalInvestedAmount')->willReturn(8000.0);

    $command = new InvestInLoanCommand(1, 101, 3000.0); // Tries to invest 3000 when only 2000 is left

    $this->useCase->execute($command);
  }
}