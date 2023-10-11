<?php

namespace App\Models;

class MessageLog extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'msg',
        'level',
        'origin_id',
        'station_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    //Default attributes
    protected $attributes = [];

    protected $appends = [];
}
