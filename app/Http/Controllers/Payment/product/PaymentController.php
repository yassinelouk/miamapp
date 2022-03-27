<?php

namespace App\Http\Controllers\Payment\product;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MegaMailer;
use App\Models\BasicExtended;
use App\Models\BasicSetting;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\OfflineGateway;
use App\Models\OrderItem;
use App\Models\OrderTime;
use App\Models\PaymentGateway;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\SubOrder;
use App\Models\SubOrdersProducts;
use Carbon\Carbon;
use Session;
use Auth;
use PDF;
use Str;
use App\Models\ShippingCharge;
use App\Models\TimeFrame;
use App\Events\OrderPlaced;
use App\Table;


class PaymentController extends Controller
{
    public function paycancle()
    {
        return redirect()->route('front.checkout')->with('error', 'Payment Cancelled.');
    }

    public function qrPayCancle()
    {
        return redirect()->route('front.qrmenu.checkout')->with('error', 'Payment Cancelled.');
    }

    public function payreturn($orderNum)
    {
        $data['orderNum'] = $orderNum;
        $order = ProductOrder::where('order_number', $orderNum)->first();
        $data['order'] = $order;

        return view('front.product.success', $data);
    }

    public function qrPayReturn($orderNum)
    {
        $defaultLang = Language::where('is_default', 1)->first();
        if (!empty($defaultLang)) {
          app()->setLocale($defaultLang->code);
        }
        $data['defaultLang'] = $defaultLang;
        $itemsCount = 0;
        $fidelityTotal =0;
        $cartTotal = 0;
        $cart = session()->get('cart');
        if(!empty($cart)){
            foreach($cart as $p){
                $itemsCount += $p['qty'];
                $cartTotal += (float)$p['total'];
                $fidelityTotal += $p['qty']*$p['fidelity_score'];
            }
        }

        $data['cart'] = $cart;
        $data['itemsCount'] = $itemsCount;
        $data['cartTotal'] = $cartTotal;
        $data['fidelityTotal'] = $fidelityTotal;
        $data['orderNum'] = $orderNum;
        $data['order'] = ProductOrder::where('order_number', $orderNum)->first();
        Session::put('Total', $data['order']->total);
        Session::put('symbol', $data['order']->currency_symbol);
        
        Session::forget('table');
        // dd(session()->all());
        return view('front.qrmenu.success',$data);


    }
    public function orderFidelityPoints() {
        $cart = Session::get('cart');
        $points_total = 0;
        if(!empty($cart)) {
            foreach ($cart as $key => $cartItem) {
                $points_total += $cartItem["fidelity_score"];
            }
        }
        return $points_total;
    }
    public function orderTotal($scharge) {
        $cart = Session::get('cart');
        $total = 0.00;

        if(!empty($cart)) {
            foreach ($cart as $key => $cartItem) {

            $total += $cartItem["total"];
            }
        }

        $discount = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0;
        $fidelity_discount = session()->has('fidelity_discount') && !empty(session()->get('fidelity_discount')) ? session()->get('fidelity_discount') : 0;
        $total = ($total + $scharge + tax()) - ($discount + $fidelity_discount);
        $total = round($total, 2);

        return $total;
    }

    public function qrOrderValidation() {
        $be = BasicExtended::first();
        $bs = BasicSetting::firstOrFail();

        if ($be->order_close == 1) {
            return back()->with('error', $be->order_close_message);
        }

        if (!Session::has('cart') && !Session::has('order')) {
            return back()->with('error', __('No item added to cart!'));
        }

        // get todays day & time
        $now = Carbon::now();
        $todaysDay = strtolower($now->format('l'));
        $currentTime = strtotime($now->toTimeString());
        $valid = false;

        // search in database by today's day & retrieve start & end time
        $orderTimes = OrderTime::where('day', $todaysDay)->get();
        foreach($orderTimes as $orderTime) {
            $start = strtotime($orderTime->start_time);
            $end = strtotime($orderTime->end_time);
            // check if any of the start or end time is emply,
            // then show message 'shop is closed today'
            if (empty($start) || empty($end) && count($orderTimes) == 1) {
                return back()->with('error', __('We are closed on'). ' ' . $todaysDay);
            }

            // check if current time is not between retrieved start & end time,
            // then show message 'shop is closed now'
            if ($currentTime >= $start && $currentTime <= $end) {
                $valid = true;
                break;
            }
        }
        if($valid == false) {
            return back()->with('error',  __('We take orders from :starttime to :endtime on :today', ['starttime' => $orderTime->start_time, 'endtime' => $orderTime->end_time, "today" => $todaysDay]));
        }

    }

    public function orderValidation($request) {

        $be = BasicExtended::first();
        $bs = BasicSetting::firstOrFail();

        if ($be->order_close == 1) {
            return back()->with('error', $be->order_close_message);
        }

        if (!Session::has('cart') && !Session::has('order')) {
            return back()->with('error', __('No item added to cart!'));
        }

        // get todays day & time
        $now = Carbon::now();
        $todaysDay = strtolower($now->format('l'));
        $currentTime = strtotime($now->toTimeString());
        $valid = false;
        // search in database by today's day & retrieve start & end time
        $orderTimes = OrderTime::where('day', $todaysDay)->get();


        foreach($orderTimes as $orderTime) {
            $start = strtotime($orderTime->start_time);
            $end = strtotime($orderTime->end_time);
            // check if any of the start or end time is emply,
            // then show message 'shop is closed today'
            if (empty($start) || empty($end) && count($orderTimes) == 1) {
                return back()->with('error',  __('We are closed on').' '. $todaysDay);
            }

            // check if current time is not between retrieved start & end time,
            // then show message 'shop is closed now'
            if ($currentTime >= $start && $currentTime <= $end) {
                $valid = true;
                break;
            }
        }


        if($valid == false) {
            return back()->with('error', __('We take orders from :starttime to :endtime on :today', ['starttime' => $orderTime->start_time, 'endtime' => $orderTime->end_time, "today" => $todaysDay]));
        }

        $messages = [
            'billing_fname.required' => __('The field :field is required', ['field' => __('Billing First Name')]),
            'billing_lname.required' => __('The field :field is required', ['field' => __('Billing Last Name')]),
            'shpping_fname.required' => __('The field :field is required', ['field' => __('Shipping First Name')]),
            'shpping_lname.required' => __('The field :field is required', ['field' => __('Shipping Last Name')]),
            'shpping_address.required' => __('The field :field is required', ['field' => __('Shipping Address')]),
            'shpping_city.required' => __('The field :field is required', ['field' => __('Shipping City')]),
            'shpping_country.required' => __('The field :field is required', ['field' => __('Shipping Country')]),
            'shpping_number.required' => __('The field :field is required', ['field' => __('Shipping Phone')]),
            'shpping_email.required' => __('The field :field is required', ['field' => __('Shipping Email')]),
        ];

        $rules = [
            'gateway' => 'nullable',
            'serving_method' => 'required|sometimes',
            'shpping_fname' => 'required|sometimes',
            'shpping_lname' => 'required|sometimes',
            'shpping_address' => 'required|sometimes',
            'shpping_city' => 'required|sometimes',
            'shpping_country' => 'required|sometimes',
            'shpping_number' => 'required|sometimes',
            'shpping_email' => 'required|sometimes',
            'pick_up_date' => 'required|sometimes',
            'pick_up_time' => 'required|sometimes',
            'table_number' => 'required|sometimes',
            'shipping_charge' => 'required|sometimes',
            'cardNumber' => 'required|sometimes',
            'cardCVC' => 'required|sometimes',
            'month' => 'required|sometimes',
            'year' => 'required|sometimes',
        ];

        if (!$request->has('same_as_shipping') || $request->same_as_shipping != 1) {
            $rules['billing_lname'] = 'required|sometimes';
            $rules['billing_address'] = 'required|sometimes';
            $rules['billing_city'] = 'required|sometimes';
            $rules['billing_country'] = 'required|sometimes';
            $rules['billing_email'] = 'required|sometimes';
            if ($request->serving_method !== 'on_table') {
                $rules['billing_number'] = 'required|sometimes';
                $rules['billing_fname'] = 'required';
            }
        }


        if ($request->serving_method == 'home_delivery' && $bs->postal_code == 1) {
            $rules['postal_code'] = 'required';
        }

        // return $request;
        // delivery date & time validation
        if ($request->serving_method == 'home_delivery' && $be->delivery_date_time_status == 1) {
            $rules['delivery_date'] = [
                function ($attribute, $value, $fail) use ($request, $be) {
                    if ($be->delivery_date_time_required == 1) {
                        if (!$request->has('delivery_date') || !$request->filled('delivery_date')) {
                            $fail(__('The field :field is required', ['field' => __('Delivery Date')]));

                        }
                    }
                }
            ];

            $dtRequired = 0;
            if ($be->delivery_date_time_required == 1) {
                if (!$request->has('delivery_time') || !$request->filled('delivery_time')) {
                    $rules['delivery_time'] = 'required';
                    $dtRequired = 1;
                }
            }
            if ($dtRequired == 0) {
                $rules['delivery_time'] = [
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->has('delivery_time') && $request->filled('delivery_time')) {
                            $tf = TimeFrame::find($request->delivery_time);
                            // if maximum orders limit is not unlimited
                            if (!empty($tf) && $tf->max_orders != 0) {
                                $orderCount = ProductOrder::where('order_status', '<>', 'cancelled')->where('delivery_time_start', $tf->start)->where('delivery_time_end', $tf->end)->count();
                                if ($orderCount >= $tf->max_orders) {
                                    $fail(__('Number of orders in this time frame has reached to its limit'));
                                }
                            }
                        }
                    }
                ];
            }
        }



        $onCount = PaymentGateway::where('keyword', $request->gateway)->count();


        // if this is a offline gateway
        if ($onCount == 0) {

            $objet = OfflineGateway::find($request->gateway) ;
            if($objet)$isReceipt = $objet->is_receipt;
            else $isReceipt = false ;
            // if the receipt is required
            if ($isReceipt == 1) {
                $rules['receipt'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $ext = $request->file('receipt')->getClientOriginalExtension();
                        if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    },
                ];
            }
        }



        return $request->validate($rules, $messages);
    }


    public function qrSaveOrder(Request $request, $status) {

        if(!empty($request->serving_method)) {
             $this->orderValidation($request);

        } else {
            $this->qrOrderValidation();
        }


        $shipping = NULL;
        $shippig_charge = 0;
        $total = $this->orderTotal($shippig_charge);

        // save order
        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }


        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;
        $website_title = BasicSetting::first()->website_title;

        $table = Table::where('table_no', $request['table_number'] )->first();


        if ($table->status == 1) {

            $order = new ProductOrder;

            if ($request['serving_method'] == 'on_table') {
                $order_number = time() . "";
                $order_number = substr($order_number , 4);
                $order['order_number'] = $order_number;
//              $order['order_number'] = $website_title . '-R-' . time() . '-' . Str::random(4);
                $order->token_no = $bs->token_no + 1;
                $bs->token_no = $order->token_no;
                $bs->save();
                $order->type = 'qr';
                $gtype = 'offline';
                $gt = OfflineGateway::find($request['gateway']);
                $gname = $gt?$gt->name : 'cash';
                $order->method = $gname;
                $order->gateway_type = $gtype;
                $order->currency_code = $be->base_currency_text;
                $order->currency_code_position = $be->base_currency_text_position;
                $order->currency_symbol = $be->base_currency_symbol;
                $order->currency_symbol_position = $be->base_currency_symbol_position;
                $order->serving_method = $request['serving_method'];
            }

        } else {

            $order = ProductOrder::where('table_number',$request['table_number'])->where('completed','no')->orderBy('id','desc')->first();
            if($order) $total = $total + $order->total;
        }

        if(!empty($request['billing_email']) && $request['serving_method'] == 'on_table') {
                $order->billing_fname = $request['billing_fname'];
                $order->billing_email = $request['billing_email'];
                $order->billing_number = $request['billing_number'];
                $order->table_number = $request['table_number'];
                $order->waiter_name = $request['waiter_name'];
        }
        if(!empty($request['billing_email'])) {
                // store customer in `customers` table
                $cust = Customer::where('email', $request['billing_email']);
                if ($cust->count() == 0) {
                    $customer = new Customer;
                } else {
                    $customer = $cust->first();
                }
                $customer->name = $request['billing_fname'];
                $customer->email = $request['billing_email'];
                $customer->phone = $request['billing_number'];
                $customer->save();
        }
        if(!empty($request['order_notes'])) {
            $order->order_notes = $request['order_notes'];
        }

        $order->total = round($total, 2);
        $order->tax = tax();
        $discount = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0.00;
        $fidelity_discount = session()->has('fidelity_discount') && !empty(session()->get('fidelity_discount')) ? session()->get('fidelity_discount') : 0.00;
        $order->coupon = $discount;
        $order->fidelity_discount = $fidelity_discount;

        $order['payment_status'] = "Pending";
        $order['txnid'] = $txnId;
        $order['charge_id'] = $chargeId;
        $order['user_id'] = Auth::check() ? Auth::user()->id : NULL;

        if(Auth::check() && $be->is_fidelity == 1) {
            $fidelity_points_total = $this->orderFidelityPoints();
            $auth_user = Auth::user();
            $auth_user->update(['fidelity_points'=>$auth_user->fidelity_points + $fidelity_points_total]);
            if(Session::has('fidelity_discount')) {
                $auth_user->update(['fidelity_points'=>$auth_user->fidelity_points - round((Session::get('fidelity_discount')/$be->base_fidelity_rate),0,PHP_ROUND_HALF_DOWN)]);
            }
        }



        // check disponibility
        $table = Table::where('table_no',$request['table_number'])->first();

        $order->table_number = $request['table_number'];

        if($table->status == 1){
            // si table dispo
            $table->status = 0 ;
            $table->client_session_id = session()->getId();
            $table->save();
        }

        $order->save();


        // save cart items
        $this->saveOrderItems($order,0);

        event(new OrderPlaced($order));
        Session::put('order',$order);
        Session::forget('cart');




            Session::forget('coupon');
            Session::forget('fidelity_discount');
            Session::forget('order');
            Session::forget('customer');
            $success_url = route('qr.payment.return', $order->order_number);

        return redirect($success_url);
    }



    public function saveOrder($request, $shipping, $total, $txnId=NULL, $chargeId=NULL, $gtype = 'online') {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;
        $order = new ProductOrder;
        $website_title = BasicSetting::first()->website_title;

        if ($request['serving_method'] == 'home_delivery' && $request->has('same_as_shipping') && $request['same_as_shipping'] == 1) {
            $order->billing_fname = $request['shpping_fname'];
            $order->billing_lname = $request['shpping_lname'];
            $order->billing_email = $request['shpping_email'];
            $order->billing_address = $request['shpping_address'];
            $order->billing_city = $request['shpping_city'];
            $order->billing_country = $request['shpping_country'];
            $order->billing_number = $request['shpping_number'];
        } else {
            $order->billing_fname = $request['billing_fname'];
            $order->billing_email = $request['billing_email'];
            $order->billing_number = $request['billing_number'];

            // if the 'serving method' is 'home delivery', but 'same as shipping address' is not selected
            if ($request['serving_method'] == 'home_delivery') {
                $order->billing_lname = $request['billing_lname'];
                $order->billing_address = $request['billing_address'];
                $order->billing_city = $request['billing_city'];
                $order->billing_country = $request['billing_country'];
            }
        }


        if ($request['serving_method'] == 'home_delivery') {
            $order['order_number'] = $website_title . '-L-' . time() . '-' . Str::random(4);
            $order->shpping_fname = $request['shpping_fname'];
            $order->shpping_lname = $request['shpping_lname'];
            $order->shpping_email = $request['shpping_email'];
            $order->shpping_address = $request['shpping_address'];
            $order->shpping_city = $request['shpping_city'];
            $order->shpping_country = $request['shpping_country'];
            $order->shpping_number = $request['shpping_number'];
            $order->delivery_date = $request['delivery_date'];
            if ($request['serving_method'] == 'home_delivery' && $be->delivery_date_time_status == 1) {
                if ($request->has('delivery_time') && $request->filled('delivery_time')) {
                    $tf = TimeFrame::find((int)$request->delivery_time);
                    $order->delivery_time_start = $tf->start;
                    $order->delivery_time_end = $tf->end;
                }
            }
            if ($bs->postal_code == 0 && $request->has('shipping_charge')) {
                $order->shipping_method = $shipping->title;
                $order->shipping_charge = $shipping->charge;
            } elseif ($bs->postal_code == 1) {
                $order->shipping_charge = $shipping->charge;

                $title = '';
                if (!empty($shipping->title)) {
                    $title = $shipping->title . ' - ';
                }
                $order->postal_code = $title . $shipping->postcode;
                $order->postal_code_status = 1;
            }
        }
        if ($request['serving_method'] == 'pick_up') {
            $order['order_number'] = $website_title . '-E-' . time() . '-' . Str::random(4);
            $order->pick_up_date = $request['pick_up_date'];
            $order->pick_up_time = $request['pick_up_time'];
        }
        if ($request['serving_method'] == 'on_table') {
            $order['order_number'] = $website_title . '-R-' . time() . '-' . Str::random(4);
            $order->token_no = $bs->token_no + 1;
            $bs->token_no = $order->token_no;
            $bs->save();
            $order->table_number = $request['table_number'];
            $order->waiter_name = $request['waiter_name'];
        }
        $order->order_notes = $request['order_notes'];
        $order->serving_method = $request['serving_method'];
        if ($request->ordered_from == 'website') {
            $order->type = 'website';
        } elseif ($request->ordered_from == 'qr') {
            $order->type = 'qr';
        }


        $order->total = round($total, 2);
        if ($gtype == 'offline') {
            $gt = OfflineGateway::findOrFail($request['gateway']);
            $gname = $gt->name;
        } else {
            $gname = $request['gateway'];
        }
        $order->method = $gname;
        $order->gateway_type = $gtype;
        $order->currency_code = $be->base_currency_text;
        $order->currency_code_position = $be->base_currency_text_position;
        $order->currency_symbol = $be->base_currency_symbol;
        $order->currency_symbol_position = $be->base_currency_symbol_position;
        $order->tax = tax();
        $discount = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0.00;
        $fidelity_discount = session()->has('fidelity_discount') && !empty(session()->get('fidelity_discount')) ? session()->get('fidelity_discount') : 0.00;
        $order->coupon = $discount;
        $order->fidelity_discount = $fidelity_discount;

        $order['payment_status'] = "Pending";
        $order['txnid'] = $txnId;
        $order['charge_id'] = $chargeId;
        $order['user_id'] = Auth::check() ? Auth::user()->id : NULL;
        if(Auth::check() && $be->is_fidelity == 1) {
            $fidelity_points_total = $this->orderFidelityPoints();
            $auth_user = Auth::user();
            $auth_user->update(['fidelity_points'=>$auth_user->fidelity_points + $fidelity_points_total]);
            if(Session::has('fidelity_discount')) {
                $auth_user->update(['fidelity_points'=>$auth_user->fidelity_points - round((Session::get('fidelity_discount')/$be->base_fidelity_rate),0, PHP_ROUND_HALF_DOWN)]);
            }
        }

        if ($request->hasFile('receipt')) {
            $receipt = uniqid() . '.' . $request->file('receipt')->getClientOriginalExtension();
            $request->file('receipt')->move('assets/front/receipt/', $receipt);
            $order['receipt'] = $receipt;
        }

        $order->save();


        // store customer in `customers` table
        $cust = Customer::where('email', $request->billing_email);
        if ($cust->count() == 0) {
            $customer = new Customer;
        } else {
            $customer = $cust->first();
        }
        $customer->name = $request->billing_fname;
        $customer->email = $request->billing_email;
        $customer->phone = $request->billing_number;
        if ($request['serving_method'] == 'home_delivery') {
            $customer->address = $request->billing_address;
        }
        $customer->save();
        Session::forget('fidelity_discount');

        return $order;
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

    public function mailFromAdmin($order) {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;

        $fileName = Str::random(4) . time() . '.pdf';
        $path = 'assets/front/invoices/product/' . $fileName;
        $data['order']  = $order;
        PDF::loadView('pdf.product', $data)->save($path);


        ProductOrder::where('id', $order->id)->update([
            'invoice_number' => $fileName
        ]);

        // Send Mail to Buyer

        $mailer = new MegaMailer;
        $data = [
            'toMail' => $order->billing_email,
            'toName' => $order->billing_fname,
            'attachment' => $fileName,
            'customer_name' => $order->billing_fname,
            'order_number' => $order->order_number,
            'order_link' => "<a href='" . route('user-orders-details',$order->id) . "'>" . route('user-orders-details',$order->id) . "</a>",
            'website_title' => $bs->website_title,
            'templateType' => 'food_checkout',
            'type' => 'foodCheckout'
        ];

        $mailer->mailFromAdmin($data);
    }

    public function mailToAdmin($order) {
        $subject = __('New Order Received!');
        $body = __('A new order has been placed').".<br>
        <strong>".__('Order Number').": </strong> " . $order->order_number . "<br>
        <a href='" . route('admin.product.details', $order->id) . "'>".__('Click here to view order details')."</a>";
        $data = [
            'fromMail' => $order->billing_email,
            'fromName' => $order->billing_fname,
            'subject' => $subject,
            'body' => $body
        ];
        $mailer = new MegaMailer;
        $mailer->mailToAdmin($data);
    }
}
