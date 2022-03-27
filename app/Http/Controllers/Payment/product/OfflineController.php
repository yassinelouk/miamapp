<?php

namespace App\Http\Controllers\Payment\product;

use App\Events\OrderPlaced;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\product\PaymentController;
use App\Models\BasicSetting;
use App\Models\PostalCode;
use App\Models\ShippingCharge;
use App\Models\OrderItem;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\SubOrder;
use App\Models\SubOrdersProducts;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use Str;


class OfflineController extends PaymentController
{

    public function store(Request $request)
    {

        $bs = BasicSetting::select('postal_code')->firstOrFail();

        if ($request->serving_method == 'home_delivery') {
            if ($bs->postal_code == 0) {
                if ($request->has('shipping_charge')) {
                    $shipping = ShippingCharge::findOrFail($request->shipping_charge);
                    $shippig_charge = $shipping->charge;
                } else {
                    $shipping = NULL;
                    $shippig_charge = 0;
                }
            } else {
                $shipping = PostalCode::findOrFail($request->postal_code);
                $shippig_charge = $shipping->charge;
            }
        } else {
            $shipping = NULL;
            $shippig_charge = 0;
        }

        $total = $this->orderTotal($shippig_charge);

        // save order
        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
        $order = $this->saveOrder($request, $shipping, $total, $txnId, $chargeId, 'offline');

        $this->saveOrderItems($order , 0);

        // send mail to buyer
        // $this->mailFromAdmin($order);
        // send mail to admin
        // $this->mailToAdmin($order);

        Session::forget('coupon');
        Session::forget('cart');
        if(Session::has('order')) {
            Session::forget('order');
        }
        event(new OrderPlaced($order));

        if ($request->ordered_from == 'website') {
            $success_url = route('product.payment.return', $order->order_number);
        } elseif ($request->ordered_from == 'qr') {
            $success_url = route('qr.payment.return', $order->order_number);
        }
        return redirect($success_url);

    }

        public function saveOrderItems($order,$state) {
        $cart = Session::get('cart');


        $sub_order_drink = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 0 ,'state' => $state],
            []
        );

        $sub_order_dish = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 1 ,'state' => $state],
            []
        );

        $sub_order_drink->touch();
        $sub_order_dish->touch();

        if(!empty($cart)) {
            foreach ($cart as $key => $cartItem) {

            $addonTotal = 0.00;
            if (!empty($cartItem["addons"])) {
                foreach ($cartItem["addons"] as $key => $addon) {
                    $addonTotal += (float)$addon["price"];
                }
                $addonTotal = $addonTotal * (int)$cartItem["qty"];
            }
            $vprice = !empty($cartItem["variations"]) ? (float)$cartItem["variations"]["price"] * (int)$cartItem["qty"] : 0.00;
            $pprice = (float)$cartItem["product_price"] * (int)$cartItem["qty"];

            $orderitem = OrderItem::where('product_order_id',$order->id)->where('product_id',$cartItem["id"])
                                        ->where('notes',$cartItem["notes"])
                                        ->where('addons',json_encode($cartItem["addons"]))
                                        ->where('variations',json_encode($cartItem["variations"]))
                                        ->first();
            if($orderitem == null ){
                $orderitem = new OrderItem();
                $orderitem->product_order_id  =  $order->id;
                $orderitem->product_id  =  $cartItem["id"];
                $orderitem->user_id  = Auth::check() ? Auth::user()->id : NULL;
                $orderitem->title  = $cartItem["name"];
                $orderitem->variations  =  json_encode($cartItem["variations"]);
                $orderitem->addons  =  json_encode($cartItem["addons"]);
                $orderitem->notes  = $cartItem["notes"];
                $orderitem->variations_price  = $vprice;
                $orderitem->addons_price = $addonTotal;
                $orderitem->product_price  =  $pprice;
                $orderitem->total  =  $pprice + $vprice + $addonTotal;
                $orderitem->qty  =  $cartItem["qty"];  // updating global quantity
                $orderitem->image  =  $cartItem["photo"];
                $orderitem->created_at =  Carbon::now();
                $orderitem->save();

            }else{
                $orderitem->qty = $orderitem->qty +  $cartItem["qty"];
                $orderitem->total = $orderitem->total +  $cartItem["qty"]*$orderitem->product_price;
                $orderitem->save();
            }



            $category = Product::find($cartItem["id"])->category;


            if($category->type == 0){
                // drink
                $sub_order_product = SubOrdersProducts::firstOrCreate(
                    ['orders_item_id' => $orderitem->id,'sub_order_id' => $sub_order_drink->id]
                );
            }
            else{
                $sub_order_product =  SubOrdersProducts::firstOrCreate(
                    ['orders_item_id' => $orderitem->id,'sub_order_id' => $sub_order_dish->id]
                );
            }

            $sub_order_product->quantity = $sub_order_product->quantity + $cartItem["qty"];  // updating local quantity of each sub order
            $sub_order_product->save();
         }
        }
    }

}
