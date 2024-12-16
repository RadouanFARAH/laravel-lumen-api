<?php

namespace App\Models;

use App\Scope\InvalidWalletTransactionScope;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'owner_id', 'credit_wallet_id', 'transaction_id'];
    protected $appends = ['reason', 'movement_type', 'balance', 'sender', 'receiver'];

    protected static function boot()
    {
        parent::boot();

        //Exclude all wallet with unsuccessfully transaction
        static::addGlobalScope(new InvalidWalletTransactionScope());
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }

    public function getReasonAttribute()
    {
        return $this->attributes['reason'] = Transaction::find($this->transaction_id)->reason;
    }

    public function getMovementTypeAttribute()
    {
        return $this->attributes['movement_type'] = !empty($this->withdrawal) ? "WITHDRAWAL" : ((!empty($this->receiver())) ? "TRANSFER" : Transaction::find($this->transaction_id)->movement->label);
    }

    public function receiver()
    {
        return Wallet::where("credit_wallet_id", $this->id)->where("id", "!=", $this->id)->first();
    }

    public function getBalanceAttribute()
    {
        return $this->attributes['balance'] = Transaction::find($this->transaction_id)->balance;
    }

    public function getCurrencyAttribute()
    {
        return $this->attributes['currency'] = Transaction::with(["country"])->find($this->transaction_id)->country->currency;
    }

    public function getSenderAttribute()
    {
        $sender = $this->sender();
        if (!empty($sender)) {
            return $this->attributes['sender'] = User::find($sender->owner_id);
        }
        return $this->attributes['sender'] = null;
    }

    public function sender()
    {
        return Wallet::find($this->credit_wallet_id);
    }

    public function getReceiverAttribute()
    {
        $receiver = $this->receiver();
        if (!empty($receiver)) {
            return $this->attributes['receiver'] = User::find($receiver->owner_id);
        }
        return $this->attributes['receiver'] = User::find($this->owner_id);
    }

    public function owner()
    {
        return $this->belongsTo("App\Models\User", 'owner_id');
    }

    public function luggageRequests()
    {
        return $this->hasMany("App\Models\LuggageRequestWallet", 'wallet_id');
    }
}
