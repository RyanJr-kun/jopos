<?php

namespace App\Observers;

use App\Models\SalePayment;
use App\Services\CashFlowSyncService;

class SalePaymentObserver
{
  public function __construct(private CashFlowSyncService $sync) {}

  public function created(SalePayment $payment): void
  {
    $this->sync->syncFromSalePayment($payment);
  }

  public function updated(SalePayment $payment): void
  {
    $this->sync->syncFromSalePayment($payment);
  }

  public function deleted(SalePayment $payment): void
  {
    $this->sync->removeSalePaymentSync($payment);
  }

  /**
   * Handle the PurchasePayment "restored" event.
   */
  public function restored(SalePayment $payment): void
  {
    //
  }

  /**
   * Handle the PurchasePayment "force deleted" event.
   */
  public function forceDeleted(SalePayment $payment): void
  {
    //
  }
}
