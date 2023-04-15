<?php

namespace FleetCart\Http\Controllers;

use FleetCart\Http\Requests\User\UpdateUserRequest;
use FleetCart\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Order\Entities\Order;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request,User $user)
    {
        $user = auth('api')->user();
        $user->update($request->validated());
        return response()->json($user);
    }

    public function updatePassword(){
         $this->validate(request(),[
             'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

         if (!Hash::check(request('current_password'), auth('api')->user()->password)) {
             return response()->json(['message' => 'Current password is incorrect'], 422);
         }

        $user = auth('api')->user();
        $user->update([
            'password' => bcrypt(request('password'))
        ]);
        return response()->json($user);
    }

    public function recentOrders(){
        $user = auth('api')->user();
        $orders = $user->recentOrders(5);
        return response()->json($orders);
    }

    public function orders(){
        $user = auth('api')->user();
       $orders =  Order::query()->with('products')->where('customer_email',$user->email)->paginate(10);
        return response()->json($orders);
    }

    public function index(){

    }



}
