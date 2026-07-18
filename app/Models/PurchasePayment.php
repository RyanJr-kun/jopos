<?php

namespace App\Models;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Models\Purchase;

class PurchasePayment extends Model
{
  protected $guarded = ['id'];

  public function purchase(): BelongsTo
  {
    return $this->belongsTo(Purchase::class, 'purchase_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function account(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'account_id');
  }
}
