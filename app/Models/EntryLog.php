<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EntryLog extends BaseModel
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

    public function scopeEnterOnly($query)
    {
        return $query->whereNotNull('enter_time')->whereNull('exit_time');
    }

    public function scopeByCostCenter($query, User $user)
    {
        return $query->when($user != null && $user->cost_center_id != null, function ($q) use ($user) {
            $q->whereHas('employee', function ($q) use ($user) {
                $q->where('cost_center_id', $user->cost_center_id);
            });
        });
    }

    //Static Functions Below Here

    /*
    * Build Table Header
    */
    public static function header()
    {
        $headers = array();
        return array_merge($headers, [
            ['field' => 'employee.card_id', 'title' => 'Card', 'sortable' => true],
            ['field' => 'employee.name', 'title' => 'Name', 'sortable' => true],
            ['field' => 'station.name', 'title' => 'Station', 'sortable' => true],
            ['field' => 'enter_time', 'title' => 'Entry', 'sortable' => true],
            ['field' => 'exit_time', 'title' => 'Exit', 'sortable' => true],
            ['field' => 'stay_duration_seconds', 'title' => 'Duration', 'sortable' => true],
        ]);
    }
}
