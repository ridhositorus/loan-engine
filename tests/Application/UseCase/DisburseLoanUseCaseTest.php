<?php
namespace Tests\Application\UseCase;

use App\Application\UseCase\DisburseLoan\{DisburseLoanCommand, DisburseLoanUseCase};
use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;
use App\Domain\Loan\Loan;
use App\Domain\Loan\LoanRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DisburseLoanUseCaseTest extends TestCase
{
  private LoanRepositoryInterface $loanRepoMock;
  private DisburseLoanUseCase $useCase;

  protected function setUp(): void
  {
    $this->loanRepoMock = $this->createMock(LoanRepositoryInterface::class);
    $this->useCase = new DisburseLoanUseCase($this->loanRepoMock);
  }

  public function testShouldSuccessfullyDisburseAnInvestedLoan(): void
  {
    // Arrange
    $loan = new Loan(1, 1, 5000, 10, 8, 'invested');
    $this->loanRepoMock->method('findAndLockById')->willReturn($loan);
    $this->loanRepoMock->expects($this->once())->method('save');

    $command = new DisburseLoanCommand(1, 202, 'c2lnbmVkX2FncmVlbWVudA==');

    // Act
    $this->useCase->execute($command);

    // Assert
    $this->assertEquals('disbursed', $loan->getStatus());
  }

  /**
   * @dataProvider invalidStateProvider
   */
  public function testShouldThrowExceptionWhenDisbursingFromInvalidState(string $invalidState): void
  {
    // Assert
    $this->expectException(InvalidLoanStateTransitionException::class);

    // Arrange
    $loan = new Loan(1, 1, 5000, 10, 8, $invalidState);
    $this->loanRepoMock->method('findAndLockById')->willReturn($loan);

    $command = new DisburseLoanCommand(1, 202, 'c2lnbmVkX2FncmVlbWVudA==');

    // Act
    $this->useCase->execute($command);
  }

  public static function invalidStateProvider(): array
  {
    return [
      'proposed state' => ['proposed'],
      'approved state' => ['approved'],
      'disbursed state' => ['disbursed'],
    ];
  }
}