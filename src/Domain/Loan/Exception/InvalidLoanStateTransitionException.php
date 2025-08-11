<?php
namespace App\Domain\Loan\Exception;

class InvalidLoanStateTransitionException extends \DomainException
{
  public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}