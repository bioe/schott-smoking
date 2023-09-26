<?php

namespace App\Models;


class CostCenter extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
    ];

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $value != null ? strtoupper($value) : null,
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
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
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }

    //View related Cost Center Employee Only
    public function scopeByCostCenter($query, User $user)
    {
        return $query->when($user != null && $user->cost_centers->count() > 0, function ($q) use ($user) {
            $q->whereIn('id', $user->cost_centers->pluck('id'));
        });
    }
}
