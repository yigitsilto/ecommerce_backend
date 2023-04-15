<?php

namespace FleetCart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSnaphot extends Model
{
    use HasFactory;

    protected $table = 'order_snapshot';

    protected $guarded = ['id'];

}
