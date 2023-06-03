<?php

namespace Modules\User\Entities;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Address\Entities\Address;
use Modules\Address\Entities\DefaultAddress;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\Product;
use Modules\Review\Entities\Review;
use Modules\User\Admin\UserTable;
use Modules\User\Repositories\Permission;

class Company extends EloquentUser implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = "company";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'company_price_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    public function companyPrice()
    {
        return $this->belongsTo(CompanyPrice::class);
    }


}
