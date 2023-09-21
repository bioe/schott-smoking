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
        return $query->when($user != null && $user->cost_center_id != null, function ($q) use ($user) {
            $q->where('id', $user->cost_center_id);
        });
    }
}
