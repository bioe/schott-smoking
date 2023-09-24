<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Banner extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'type',
        'filename',
        'path',
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
        'full_path'
    ];

    public function active(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? true : false
        );
    }

    public function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => !empty($attributes['path']) ? url('storage/' . $attributes['path']) : null
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
            ['field' => 'title', 'title' => 'Title', 'sortable' => true],
            ['field' => 'filename', 'title' => 'Filename'],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
