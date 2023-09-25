<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;


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
        'stay_duration_seconds', //From station setting
        'actual_stay_duration_seconds', //How long is the stay enter + exit time
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

    protected $appends = [
        'finished_at'
    ];

    public function finishedAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::createFromFormat(date_extract_format($attributes['enter_time']), $attributes['enter_time'])->addSeconds($attributes['stay_duration_seconds'])
        );
    }

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

    //View related Cost Center Employee Only
    public function scopeByCostCenter($query, User $user)
    {
        return $query->when($user != null && $user->cost_centers->count() > 0, function ($q) use ($user) {
            $q->whereHas('employee', function ($q) use ($user) {
                $q->whereIn('cost_center_id', $user->cost_centers->pluck('id'));
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
            ['field' => 'actual_stay_duration_seconds', 'title' => 'Duration', 'sortable' => true],
        ]);
    }
}
