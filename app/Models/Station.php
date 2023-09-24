<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Station extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'max_pax', //How many person allow to enter
        'stay_duration_seconds', //How long they allow to smoke
        'warning_below_seconds', //Warning when the time reach below
        'disable_next_entry_seconds', //Prevent re-entry
        'door_open_seconds', //IO to turn off the door again
        'annoucement_interval', //Annoucement slider
        'banner_interval', //Banner slider
        'ip',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_pax' => 'integer',
        'active' => 'boolean',
    ];

    //Default attributes
    protected $attributes = [
        'active' => true,
    ];

    protected $appends = [
        'url'
    ];

    public function active(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? true : false
        );
    }

    public function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper($value)
        );
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => route('area', $attributes['code'])
        );
    }

    //Static Functions Below Here

    /*
    * Build Table Header
    */
    public static function header()
    {
        $headers = array();
        return array_merge($headers, [
            ['field' => 'code', 'title' => 'Code', 'sortable' => true],
            ['field' => 'name', 'title' => 'Name', 'sortable' => true],
            ['field' => 'max_pax', 'title' => 'Max Pax', 'sortable' => true],
            ['field' => 'stay_duration_seconds', 'title' => 'Stay Duration', 'sortable' => true],
            ['field' => 'warning_below_seconds', 'title' => 'Warning Time', 'sortable' => true],
            ['field' => 'disable_next_entry_seconds', 'title' => 'Next Entry', 'sortable' => true],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
