<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Sensor extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'station_id',
        'type', //Air | Temperature
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    //Default attributes
    protected $attributes = [];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    //Static Functions Below Here

    /*
    * Build Table Header
    */
    public static function header()
    {
        $headers = array();
        return array_merge($headers, [
            ['field' => 'type', 'title' => 'Type', 'sortable' => true],
            ['field' => 'value', 'title' => 'Value', 'sortable' => true],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
