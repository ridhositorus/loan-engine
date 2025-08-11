<?php

use App\Infrastructure\Http\Action\ApproveLoanAction;
use App\Infrastructure\Http\Action\CreateLoanAction;
use App\Infrastructure\Http\Action\DisburseLoanAction;
use App\Infrastructure\Http\Action\GetLoanDetailAction;
use App\Infrastructure\Http\Action\InvestInLoanAction;
use App\Infrastructure\Http\Action\CreateUserAction;
use Slim\Routing\RouteCollectorProxy;

return function(RouteCollectorProxy $group) {

  /**
   * User Management
   * A single endpoint is shown for simplicity.
   */
  $group->post('/users', CreateUserAction::class);

  /**
   * Loan Lifecycle Management
   * These routes cover the entire loan process from creation to disbursement.
   */
  $group->post('/loans', CreateLoanAction::class);
  $group->get('/loans/{id:[0-9]+}', GetLoanDetailAction::class);
  $group->post('/loans/{id:[0-9]+}/approve', ApproveLoanAction::class);
  $group->post('/loans/{id:[0-9]+}/invest', InvestInLoanAction::class);
  $group->post('/loans/{id:[0-9]+}/disburse', DisburseLoanAction::class);
};