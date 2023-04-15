<?php

namespace FleetCart\Http\Controllers;

use FleetCart\WishList;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    public function index()
    {
        $wishlists = WishList::query()
            ->whereHas('product')
            ->with(['product','user'])
            ->where('user_id',auth('api')->id())
            ->latest()
            ->paginate(20);
        return response()->json($wishlists);
    }

    public function store(Request $request)
    {
        // validate the product_id
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        $wishlist = WishList::query()
            ->where('user_id',auth('api')->id())
            ->where('product_id',$request->product_id)
            ->first();
        if($wishlist){
            return response()->json($wishlist);
        }
        $wishlist = WishList::create([
            'user_id' => auth('api')->id(),
            'product_id' => $request->product_id
        ]);
        return response()->json($wishlist);

    }

    public function destroy($id,Request  $request)
    {
        $wishlist = WishList::query()
            ->where('user_id',auth('api')->user()->id)
            ->where('product_id',$id)
            ->delete();
        return response()->json(['message' => 'Wishlist item deleted']);
    }
}
