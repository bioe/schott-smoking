<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Annoucement extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'station_id',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    //Default attributes
    protected $attributes = [
        'active' => true,
    ];

    protected $appends = [
        'html_content'
    ];

    public function active(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? true : false
        );
    }
    public function htmlContent(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => nl2br($attributes['content'] ?? '')
        );
    }

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
            ['field' => 'title', 'title' => 'Title', 'sortable' => true],
            ['field' => 'content', 'title' => 'Content', 'sortable' => true],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
