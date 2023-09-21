<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BaseModel extends Model
{
    public function scopeFilterSort($query, $filters)
    {
        return $query->when(!empty($filters['sort']['field']), function ($q) use ($filters) {
            $q->orderBy($filters['sort']['field'], $filters['sort']['direction']);
        });
    }

    public function scopeStartOfDay($query, $field, $date)
    {
        $start = $date ? Carbon::createFromFormat('Y-m-d', $date)->startOfDay() : null;

        return $query->when($start, function ($q) use ($start, $field) {
            $q->where($field, '>=', $start);
        });
    }

    public function scopeEndOfDay($query, $field, $date)
    {
        $end = $date ? Carbon::createFromFormat('Y-m-d', $date)->endOfDay() : null;

        return $query->when($end, function ($q) use ($end, $field) {
            $q->where($field, '<=', $end);
        });
    }

    public function scopeDateRange($query, $start_date, $end_date)
    {
        $start = $start_date ? Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay() : null;
        $end = $end_date ? Carbon::createFromFormat('Y-m-d', $end_date)->endOfDay() : null;

        return $query->when($start, function ($q) use ($start) {
            $q->where('created_at', '>=', $start);
        })->when($end, function ($q) use ($end) {
            $q->where('created_at', '<=', $end);
        });
    }
}
