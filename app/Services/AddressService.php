<?php

namespace FleetCart\Services;

use FleetCart\Repositories\Address\AddressRepositoryInterface;

class AddressService
{

    private $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function index(){
        return $this->addressRepository->index();
    }
}
