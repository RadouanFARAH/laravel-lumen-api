<?php

namespace App\Models;

use App\Scope\InvalidWalletTransactionScope;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $incrementing = false;
    protected $fillable = ['balance', 'id', 'account',  'reason', 'payment_token', 'movement_type_id','provider_id','country_id'];

    public function movement(){
        return $this->belongsTo(MovementType::class,'movement_type_id');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function provider(){

        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }

}
