<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RawEntryLog extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'station_id',
        'card_hex',
        'card_id',
        'enter_time',
        'exit_time',
        'stay_duration_seconds',
        'disable_next_entry_seconds',
        'overstay_seconds',
        'maintenance'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enter_time' => 'datetime',
        'exit_time' => 'datetime',
        'maintenance' => 'boolean'
    ];

    //Default attributes
    protected $attributes = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function scopeComplete($query)
    {
        return $query->whereNotNull('enter_time')->whereNotNull('exit_time');
    }

    //Static Functions Below Here

    /*
    * Build Table Header
    */
    public static function header()
    {
        $headers = array();
        return array_merge($headers, [
            ['field' => 'user.name', 'title' => 'Name', 'sortable' => true],
            ['field' => 'station.name', 'title' => 'Station', 'sortable' => true],
            ['field' => 'enter_time', 'title' => 'Entry', 'sortable' => true],
            ['field' => 'exit_time', 'title' => 'Exit', 'sortable' => true],
        ]);
    }
}
