<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Address\Entities\Address;

class AddressController extends Controller
{
    private $addressService;
    public function __construct(AddressService  $addressService){
        $this->addressService = $addressService;
    }
    public function delete($address){
        Address::query()->where('id',$address)->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }
    public function index(){
        return $this->addressService->index();
    }

    public function update(Request $request, $address){
        $this->validate($request,[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $addressToUpdate = Address::query()->findOrFail($address);

        if ($addressToUpdate->customer_id != auth('api')->user()->getAuthIdentifier()){
            return response()->json(['error'=> 'unauthrorized'],401);
        }

        $addressToUpdate->update($request->all());
        return response()->json($address);
    }

    public function store(Request $request){
        $this->validate($request,[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);
        $request->merge(['customer_id' => auth('api')->user()->getAuthIdentifier()]);
        $address = Address::create($request->all());
        return response()->json($address);
    }
    public function show($address){
        $address = Address::query()->findOrFail($address);
        return response()->json($address);
    }
}
