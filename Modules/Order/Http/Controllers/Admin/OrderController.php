<?php

namespace Modules\Order\Http\Controllers\Admin;

use FleetCart\Refund;
use Illuminate\Http\Request;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Order\Entities\Order;

class OrderController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'products',
        'coupon',
        'taxes'
    ];

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'order::orders.order';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'order::admin.orders';


    public function refunds()
    {
        $refunds = Refund::query()
                         ->with([
                                    'order',
                                    'user'
                                ])
                         ->orderBy('id', 'desc')
                         ->paginate();
        return view('order::admin.refunds.index', compact('refunds'));
    }

    public function refundsShow($id)
    {
        $refund = Refund::query()
                        ->with([
                                   'product',
                                   'user'
                               ])
                        ->findOrFail($id);
        return view('order::admin.refunds.show', compact('refund'));
    }

    public function refundsUpdate($id,Request $request)
    {

        // update the status for refudn model

        $refund = Refund::query()
                        ->with([
                                   'product',
                                   'user'
                               ])
                        ->findOrFail($id);

        $refund->status = $request->status;
        $refund->save();

        // return back to the refunds page
        return redirect()->route('admin.refunds.index');

    }

}
