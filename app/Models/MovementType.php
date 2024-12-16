<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    use HasFactory;
    public const DEPOSIT=1;
    public const WITHDRAWAL=2;
    public const INTERNAL_MOVEMENT=3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'label',
    ];

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
}
