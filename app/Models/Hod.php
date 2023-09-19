<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Hod extends BaseModel
{
    public $table = "hod";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
            ['field' => 'name', 'title' => 'Name', 'sortable' => true],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
        ]);
    }

    //View related HOD Employee Only
    public function scopeByHod($query, User $user)
    {
        return $query->when($user != null && $user->hod_id != null, function ($q) use ($user) {
            $q->where('id', $user->hod_id);
        });
    }
}
