<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }
}
