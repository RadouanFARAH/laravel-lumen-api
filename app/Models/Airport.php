<?php

namespace App\Models;


class Airport extends BaseModel
{
    protected $guarded =[];

    public $searchable = [
        'columns' => [
            'airports.name' => 10,
            'airports.code' => 10,
            'countries.name' => 10,
            'cities.name' => 10,
        ],
        'joins' => [
            'cities' => ['airports.city_id', 'cities.id'],
            'countries' => ['cities.country_id', 'countries.id'],
        ],
    ];

    public function getCoordinatesAttribute($coordinates)
    {
        return json_decode($coordinates);
    }

    public function city(){
        return $this->belongsTo("App\Models\City");
    }
}