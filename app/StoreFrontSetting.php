<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreFrontSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'storefront_settings';
}
