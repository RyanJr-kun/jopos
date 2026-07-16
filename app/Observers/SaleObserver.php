<?php

namespace App\Observers;

use App\Services\CashFlowSyncService;
use Modules\POS\Models\Sale;

class SaleObserver
{
  public function __construct(private CashFlowSyncService $sync) {}
  /**
   * Handle the Sale "created" event.
   */
  public function created(Sale $sale): void
  {
    //
  }

  /**
   * Handle the Sale "updated" event.
   */
  public function updated(Sale $sale): void
  {
    if (!$sale->wasChanged('status_pembayaran')) {
      return;
    }

    if ($sale->status_pembayaran === 'Batal') {
      $this->sync->cancelForSale($sale);
    } elseif ($sale->getOriginal('status_pembayaran') === 'Batal') {
      $this->sync->reactivateForSale($sale);
    }
  }

  /**
   * Handle the Sale "deleted" event.
   */
  public function deleted(Sale $sale): void
  {
    //
  }

  /**
   * Handle the Sale "restored" event.
   */
  public function restored(Sale $sale): void
  {
    //
  }

  /**
   * Handle the Sale "force deleted" event.
   */
  public function forceDeleted(Sale $sale): void
  {
    //
  }
}
