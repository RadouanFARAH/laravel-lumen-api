<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    public const PAYPAL=1;
    public const STRIPE=2;
    public const DOHONE=3;
    public const LUGGIN=4;


    public function transactions(){
    	return $this->hasMany(Transaction::class);
    }
}
