<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LuggageRequestWallet extends Model
{
    protected $fillable =["wallet_id","luggage_request_id"];

    protected static function boot()
    {
        parent::boot();

        //Exclude all wallet with unsuccessfully transaction
        static::addGlobalScope(new class implements Scope{

            public function apply(Builder $builder, Model $model)
            {
                $builder->whereHas('wallet');
            }
        });
    }

    public function wallet(){
        return $this->belongsTo("App\Models\Wallet");
    }

    public function request(){
        return $this->belongsTo("App\Models\LuggageRequest","luggage_request_id");
    }
}
