<?php
namespace FleetCart\Repositories\Address;
use Doctrine\DBAL\Query\QueryBuilder;
use Modules\Address\Entities\Address;

class AddressRepository implements AddressRepositoryInterface
{
    public function index()
    {
        return Address::query()->where('customer_id',auth('api')->user()->id)->get();
    }

}
