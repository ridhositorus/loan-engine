<?php
namespace Tests\Application\UseCase;

use App\Application\UseCase\ApproveLoan\ApproveLoanCommand;
use App\Application\UseCase\ApproveLoan\ApproveLoanUseCase;
use App\Domain\Loan\Exception\InvalidLoanStateTransitionException;
use App\Domain\Loan\Loan;
use App\Domain\Loan\LoanRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ApproveLoanUseCaseTest extends TestCase
{
  private LoanRepositoryInterface $loanRepositoryMock;
  private ApproveLoanUseCase $useCase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->loanRepositoryMock = $this->createMock(LoanRepositoryInterface::class);
    $this->useCase = new ApproveLoanUseCase($this->loanRepositoryMock);
  }

  public function testShouldApproveLoanWhenInProposedState(): void
  {
    // Arrange
    $loan = new Loan(1, 1, 5000, 10, 8, 'proposed');
    $this->loanRepositoryMock->method('findAndLockById')->with(1)->willReturn($loan);
    $this->loanRepositoryMock->expects($this->once())->method('save');

    $command = new ApproveLoanCommand(1, 101, 'cGljdHVyZV9kYXRh');
    $agreementLink = $this->useCase->execute($command);

    $this->assertNotNull($agreementLink);
    $this->assertEquals('approved', $loan->getStatus());
  }

  /**
   * @dataProvider invalidStateProvider
   */
  public function testShouldThrowExceptionWhenApprovingLoanInInvalidState(string $invalidState): void
  {
    $this->expectException(InvalidLoanStateTransitionException::class);

    $loan = new Loan(1, 1, 5000, 10, 8, $invalidState);
    $this->loanRepositoryMock->method('findAndLockById')->with(1)->willReturn($loan);
    $command = new ApproveLoanCommand(1, 101, 'cGljdHVyZV9kYXRh');

    $this->useCase->execute($command);
  }

  public static function invalidStateProvider(): array
  {
    return [
      'approved state' => ['approved'],
      'invested state' => ['invested'],
      'disbursed state' => ['disbursed'],
    ];
  }
}