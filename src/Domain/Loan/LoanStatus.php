<?php

namespace App\Domain\Loan;

enum LoanStatus: string
{
  case PROPOSED = 'proposed';
  case APPROVED = 'approved';
  case INVESTED = 'invested';
  case DISBURSED = 'disbursed';
}