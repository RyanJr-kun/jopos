<?php

namespace App\Services;

use App\Models\CashFlow;
use App\Models\PurchasePayment;
use App\Models\SalePayment;
use Modules\Inventory\Models\Purchase;
use Modules\POS\Models\Sale;

class CashFlowSyncService
{
  // ==================== SALE ====================

  public function syncFromSalePayment(SalePayment $payment): void
  {
    $sale = $payment->sale;

    CashFlow::updateOrCreate(
      ['source_type' => 'sale_payment', 'source_id' => $payment->id],
      [
        'type' => CashFlow::TYPE_INCOME,
        'store_id' => $sale->store_id,
        'transaction_category_id' => null,
        'tanggal' => $payment->tanggal_bayar,
        'nominal' => $payment->jumlah_bayar,
        'metode_pembayaran' => $payment->metode_pembayaran,
        'bank_id' => $payment->bank_id,
        'referensi' => $payment->referensi_pembayaran ?: $sale->referensi . '-P' . $payment->id,
        'keterangan' => 'Pembayaran Penjualan ' . $sale->referensi,
        'description' => $payment->catatan,
        'user_id' => $payment->user_id,
      ],
    );
  }

  public function removeSalePaymentSync(SalePayment $payment): void
  {
    CashFlow::where('source_type', 'sale_payment')
      ->where('source_id', $payment->id)
      ->update(['dibatalkan_at' => now()]);
  }

  public function cancelForSale(Sale $sale): void
  {
    $paymentIds = $sale->payments()->pluck('id');

    CashFlow::where('source_type', 'sale_payment')
      ->whereIn('source_id', $paymentIds)
      ->whereNull('dibatalkan_at')
      ->update(['dibatalkan_at' => now()]);
  }

  public function reactivateForSale(Sale $sale): void
  {
    $paymentIds = $sale->payments()->pluck('id');

    CashFlow::where('source_type', 'sale_payment')
      ->whereIn('source_id', $paymentIds)
      ->whereNotNull('dibatalkan_at')
      ->update(['dibatalkan_at' => null]);
  }

  // ==================== PURCHASE ====================

  public function syncFromPurchasePayment(PurchasePayment $payment): void
  {
    $purchase = $payment->purchase;

    CashFlow::updateOrCreate(
      ['source_type' => 'purchase_payment', 'source_id' => $payment->id],
      [
        'type' => CashFlow::TYPE_EXPENSE,
        'store_id' => $purchase->store_id,
        'transaction_category_id' => null,
        'tanggal' => $payment->tanggal_bayar,
        'nominal' => $payment->jumlah_bayar,
        'metode_pembayaran' => $payment->metode_pembayaran,
        'bank_id' => $payment->bank_id,
        'referensi' => $payment->referensi_pembayaran ?: $purchase->referensi . '-P' . $payment->id,
        'keterangan' => 'Pembayaran Pembelian ' . $purchase->referensi,
        'description' => $payment->catatan,
        'user_id' => $payment->user_id,
      ],
    );
  }

  public function removePurchasePaymentSync(PurchasePayment $payment): void
  {
    CashFlow::where('source_type', 'purchase_payment')
      ->where('source_id', $payment->id)
      ->update(['dibatalkan_at' => now()]);
  }

  public function cancelForPurchase(Purchase $purchase): void
  {
    $paymentIds = $purchase->payments()->pluck('id');

    CashFlow::where('source_type', 'purchase_payment')
      ->whereIn('source_id', $paymentIds)
      ->whereNull('dibatalkan_at')
      ->update(['dibatalkan_at' => now()]);
  }

  public function reactivateForPurchase(Purchase $purchase): void
  {
    $paymentIds = $purchase->payments()->pluck('id');

    CashFlow::where('source_type', 'purchase_payment')
      ->whereIn('source_id', $paymentIds)
      ->whereNotNull('dibatalkan_at')
      ->update(['dibatalkan_at' => null]);
  }
}
