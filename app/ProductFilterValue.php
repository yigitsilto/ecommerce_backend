<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterValue extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }

    public function filterValue()
    {
        return $this->belongsTo(FilterValue::class);
    }


}
