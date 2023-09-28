<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class XportalEmployee extends BaseModel
{
    protected $connection = 'sqlsrv';
    protected $table = "tbl_StaffDetails";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    //Default attributes
    protected $attributes = [];
}
