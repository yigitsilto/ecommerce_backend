<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class RelatedProduct extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function product()
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
