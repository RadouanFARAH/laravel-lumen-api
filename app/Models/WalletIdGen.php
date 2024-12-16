<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use App\Scope\InvalidWalletTransactionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WalletIdGen extends Model
{
    protected $fillable = ['movement_type_id', 'id', 'owner_id', 'credit_wallet_id', 'balance', 'transaction_id'];
    public $incrementing = false;
    protected $table = 'wallets';

    public function transaction(){
        return $this->belongsTo('App\Models\Transaction');
    }
}
