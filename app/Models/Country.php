<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends BaseModel
{
    use HasFactory;
    protected $guarded =[];
    protected $translatable=['name'];
    protected $appends=["flag"];

    public function getFlagAttribute(){
        return asset("private/flags/".strtolower($this->alpha2).".png");
    }

}
