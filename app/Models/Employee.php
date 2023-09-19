<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Employee extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'name',
        'hod_id',
        'maintenance',
        'origin_id',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'maintenance' => 'boolean'
    ];

    //Default attributes
    protected $attributes = [
        'active' => true,
        'maintenance' => false,
    ];

    public function active(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? true : false
        );
    }

    public function hod()
    {
        return $this->belongsTo(Hod::class);
    }

    //View related HOD Employee Only
    public function scopeByHod($query, User $user)
    {
        return $query->when($user != null && $user->hod_id != null, function ($q) use ($user) {
            $q->where('hod_id', $user->hod_id);
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
            ['field' => 'card_id', 'title' => 'Card ID', 'sortable' => true],
            ['field' => 'name', 'title' => 'Name', 'sortable' => true],
            ['field' => 'hod_id', 'title' => 'HOD'],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
