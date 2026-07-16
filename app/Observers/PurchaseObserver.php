<?php

namespace App\Observers;

use App\Services\CashFlowSyncService;
use Modules\Inventory\Models\Purchase;

class PurchaseObserver
{
  public function __construct(private CashFlowSyncService $sync) {}
  /**
   * Handle the Purchase "created" event.
   */
  public function created(Purchase $purchase): void
  {
    //
  }

  /**
   * Handle the Purchase "updated" event.
   */
  public function updated(Purchase $purchase): void
  {
    if (!$purchase->wasChanged('status_pembayaran')) {
      return;
    }

    if ($purchase->status_pembayaran === 'Batal') {
      $this->sync->cancelForPurchase($purchase);
    } elseif ($purchase->getOriginal('status_pembayaran') === 'Batal') {
      $this->sync->reactivateForPurchase($purchase);
    }
  }

  /**
   * Handle the Purchase "deleted" event.
   */
  public function deleted(Purchase $purchase): void
  {
    //
  }

  /**
   * Handle the Purchase "restored" event.
   */
  public function restored(Purchase $purchase): void
  {
    //
  }

  /**
   * Handle the Purchase "force deleted" event.
   */
  public function forceDeleted(Purchase $purchase): void
  {
    //
  }
}
