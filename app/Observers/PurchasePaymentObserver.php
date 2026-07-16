<?php

namespace App\Observers;

use App\Models\PurchasePayment;
use App\Services\CashFlowSyncService;

class PurchasePaymentObserver
{
  public function __construct(private CashFlowSyncService $sync) {}

  public function created(PurchasePayment $payment): void
  {
    $this->sync->syncFromPurchasePayment($payment);
  }

  public function updated(PurchasePayment $payment): void
  {
    $this->sync->syncFromPurchasePayment($payment);
  }

  public function deleted(PurchasePayment $payment): void
  {
    $this->sync->removePurchasePaymentSync($payment);
  }

  /**
   * Handle the PurchasePayment "restored" event.
   */
  public function restored(PurchasePayment $purchasePayment): void
  {
    //
  }

  /**
   * Handle the PurchasePayment "force deleted" event.
   */
  public function forceDeleted(PurchasePayment $purchasePayment): void
  {
    //
  }
}
