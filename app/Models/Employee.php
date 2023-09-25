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
        'cost_center_id',
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

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class);
    }

    //View related Cost Center Employee Only
    public function scopeByCostCenter($query, User $user)
    {
        return $query->when($user != null && $user->cost_centers->count() > 0, function ($q) use ($user) {
            $q->whereIn('cost_center_id', $user->cost_centers->pluck('id'));
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
            ['field' => 'cost_center_id', 'title' => 'Cost Center'],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }
}
