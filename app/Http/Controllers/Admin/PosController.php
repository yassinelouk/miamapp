<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MegaMailer;
use App\Models\BasicExtended;
use App\Models\BasicSetting;
use App\Models\Language;
use App\Models\OrderItem;
use App\Models\PostalCode;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\SubOrder;
use App\Models\SubOrdersProducts;
use App\Models\ServingMethod;
use App\Models\ShippingCharge;
use App\Models\TimeFrame;
use App\PosPaymentMethod;
use App\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Validator;
use Str;
use PDF;
use Illuminate\Notifications\Notification;


class PosController extends Controller
{
    public function index() {
        $lang = Language::where('is_default', 1)->firstOrFail();
        $pcats = $lang->pcategories()->where('status', 1)->get();
        $data['smethods'] = ServingMethod::where('pos', 1)->get();
        $data['pmethods'] = PosPaymentMethod::where('status', 1)->get();
        $data['tables'] = Table::get();
        $data['scharges'] = $lang->shippings;
        $data['postcodes'] = PostalCode::where('language_id', $lang->id)->orderBy('serial_number', 'ASC')->get();
        foreach ($pcats as $prod ) {
            $data['products'] = $prod['products'];
        }

        $data['pcats'] = $pcats;
        $data['cart'] = Session::get('pos_cart');
        foreach ($pcats as $prod ) {
            $data['productsss'] = $prod['products'];
        }
        // disabled days
        $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        $disDays = [];
        foreach ($days as $key => $day) {
            $count = TimeFrame::where('day', $day)->count();
            if ($count == 0) {
                if ($day == 'sunday') {
                    $disDays[] = 0;
                } elseif ($day == 'monday') {
                    $disDays[] = 1;
                } elseif ($day == 'tuesday') {
                    $disDays[] = 2;
                } elseif ($day == 'wednesday') {
                    $disDays[] = 3;
                } elseif ($day == 'thursday') {
                    $disDays[] = 4;
                } elseif ($day == 'friday') {
                    $disDays[] = 5;
                } elseif ($day == 'saturday') {
                    $disDays[] = 6;
                }
            }
        }

        $data['disDays'] = $disDays;
        $data['onTable'] = ServingMethod::where('value', 'on_table')->firstOrFail();

        return view('admin.pos.index', $data);
    }
    public function indexApi() {
        $lang = Language::where('is_default', 1)->firstOrFail();
        $pcats = $lang->pcategories()->where('status', 1)->get();
        $data['smethods'] = ServingMethod::where('pos', 1)->get();
        $data['pmethods'] = PosPaymentMethod::where('status', 1)->get();
        $data['tables'] = Table::get();
        $data['scharges'] = $lang->shippings;
        $data['postcodes'] = PostalCode::where('language_id', $lang->id)->orderBy('serial_number', 'ASC')->get();

        $data['pcats'] = $pcats;
        foreach ($pcats as $prod ) {
            $data['products'] = $prod['products'];
        }
        $data['cart'] = Session::get('pos_cart');

        // disabled days
        $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        $disDays = [];
        foreach ($days as $key => $day) {
            $count = TimeFrame::where('day', $day)->count();
            if ($count == 0) {
                if ($day == 'sunday') {
                    $disDays[] = 0;
                } elseif ($day == 'monday') {
                    $disDays[] = 1;
                } elseif ($day == 'tuesday') {
                    $disDays[] = 2;
                } elseif ($day == 'wednesday') {
                    $disDays[] = 3;
                } elseif ($day == 'thursday') {
                    $disDays[] = 4;
                } elseif ($day == 'friday') {
                    $disDays[] = 5;
                } elseif ($day == 'saturday') {
                    $disDays[] = 6;
                }
            }
        }

        $data['disDays'] = $disDays;
        $data['onTable'] = ServingMethod::where('value', 'on_table')->firstOrFail();

        return response()->json($data);
    }

    public function addToCart($id)
    {
        $cart = Session::get('pos_cart');
        $data = explode(',,,', $id);
        $id = (int)$data[0];
        $qty = (int)$data[1];
        $total = (float)$data[2];
        $variant = json_decode($data[3], true);
        $addons = json_decode($data[4], true);
        $notes = $data[5];

        $product = Product::findOrFail($id);

        // validations
        if ($qty < 1) {
            return response()->json(['error' => 'Quanty must be 1 or more than 1.']);
        }
        $pvariant = json_decode($product->variations, true);
        if (!empty($pvariant) && empty($variant)) {
            return response()->json(['error' => 'You must select a variant.']);
        }


        if (!$product) {
            abort(404);
        }
        $cart = Session::get('pos_cart');
        $ckey = time();

        // if cart is empty then this the first product
        if (!$cart) {

            $cart = [
                $ckey => [
                    "id" => $id,
                    "name" => $product->title,
                    "qty" => (int)$qty,
                    "variations" => $variant,
                    "addons" => $addons,
                    "product_price" => (float)$product->current_price,
                    "total" => $total,
                    "photo" => $product->feature_image,
                    "notes" => $notes
                ]
            ];

            Session::put('pos_cart', $cart);
            return response()->json(['message' => 'Product added to cart successfully!']);
        }

        // if cart not empty then check if this product (with same variation) exist then increment quantity
        foreach ($cart as $key => $cartItem) {
            if ($cartItem["id"] == $id && $variant == $cartItem["variations"] && $addons == $cartItem["addons"] && (isset($cartItem["notes"]) && strcasecmp($notes, $cartItem["notes"]) == 0 || !isset($cartItem["notes"]))) {
                $cart[$key]['qty'] = (int)$cart[$key]['qty'] + $qty;
                $cart[$key]['total'] = (float)$cart[$key]['total'] + $total;
                Session::put('pos_cart', $cart);
                return response()->json(['message' => 'Product added to cart successfully!']);
            }
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$ckey] = [
            "id" => $id,
            "name" => $product->title,
            "qty" => (int)$qty,
            "variations" => $variant,
            "addons" => $addons,
            "product_price" => (float)$product->current_price,
            "total" => $total,
            "photo" => $product->feature_image,
            "notes" => $notes
        ];

        Session::put('pos_cart', $cart);

        return response()->json(['message' => 'Product added to cart successfully!']);
    }

    public function updateQty($key, $qty) {
        $cart = Session::get('pos_cart');

        $total = 0;
        $cart["$key"]["qty"] = (int)$qty;

        // calculate total
        $addons = $cart["$key"]["addons"];
        if (is_array($addons)) {
            foreach ($addons as $addKey => $addon) {
                $total += (float)$addon["price"];
            }
        }
        if (is_array($cart["$key"]["variations"])) {
            $total += (float)$cart["$key"]["variations"]["price"];
        }
        $total += (float)$cart["$key"]["product_price"];
        $total = $total * $qty;

        // save total in the cart item
        $cart["$key"]["total"] = $total;

        Session::put('pos_cart', $cart);

        return 'success';
    }

    public function cartitemremove($id)
    {
        if ($id) {
            $cart = Session::get('pos_cart');
            unset($cart[$id]);
            Session::put('pos_cart', $cart);

            return response()->json(['message' => 'Item removed successfully']);
        }
    }

    public function cartClear()
    {
        // return 1;
        Session::forget('pos_cart');
        Session::flash('success', 'Cart has been cleared!');
        return "success";
    }

    public function customerCopy() {
        $data['cart'] = Session::get('pos_cart');
        return view('admin.pos.partials.customer-copy', $data);
    }

    public function kitchenCopy() {
        $data['cart'] = Session::get('pos_cart');
        return view('admin.pos.partials.kitchen-copy', $data);
    }

    public function tokenNo() {
        return view('admin.pos.partials.token-no');
    }

    public function paymentMethods() {
        $data['pmethods'] = PosPaymentMethod::all();
        return view('admin.pos.payment-methods', $data);
    }

    public function paymentMethodStore(Request $request) {
        $rules = [
            'status' => 'required',
            'name' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $pm = new PosPaymentMethod;
        $pm->status = $request->status;
        $pm->name = $request->name;
        $pm->save();

        Session::flash('success', 'Payment Method added successfully!');
        return "success";
    }

    public function paymentMethodUpdate(Request $request)
    {
        $rules = [
            'status' => 'required',
            'name' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $pm = PosPaymentMethod::findOrFail($request->pm_id);
        $pm->status = $request->status;
        $pm->name = $request->name;
        $pm->save();

        Session::flash('success', 'Payment Method updated successfully!');
        return "success";
    }

    public function paymentMethodDelete(Request $request)
    {
        $pm = PosPaymentMethod::findOrFail($request->pm_id);
        $pm->delete();

        Session::flash('success', 'Payment Method deleted successfully!');
        return back();
    }

    public function customerName($phone) {
        $customer = Customer::where('phone', $phone)->first();
        return response()->json($customer);
    }

    public function placeOrder(Request $request) {

        if (empty(Session::get('pos_cart'))) {
            return back()->with('warning', 'Le panier est vide!');
        }
        if (!$request->has('table_no')) {
            return back()->with('warning', 'Aucune table sélectionnée!');
        }

        if ($request->has('delivery_time') && $request->filled('delivery_time')) {
            $tf = TimeFrame::find((int)$request->delivery_time);
            // if maximum orders limit is not unlimited
            if (!empty($tf) && $tf->max_orders != 0) {
                $orderCount = ProductOrder::where('order_status', '<>', 'cancelled')->where('delivery_time_start', $tf->start)->where('delivery_time_end', $tf->end)->count();
                if ($orderCount >= $tf->max_orders) {
                    return back()->with('warning', "Number of orders in this time frame has reached to its limit!");
                }
            }
        }

        $be = BasicExtended::first();
        $bs = BasicSetting::first();
        $table = Table::where('table_no', $request->table_no )->first();
        if ($table->status == 1) {
            // store in `product_orders`
            $po = new ProductOrder;
            $order_number = time() . "";
            $order_number = substr($order_number , 4);
            $po->order_number = $order_number;
            $po->billing_fname = $request->customer_name;
            $po->billing_number = $request->customer_phone;
            $po->serving_method = $request->serving_method;
            $po->method = $request->payment_method;
            $po->payment_status = $request->payment_status;


            if ($request->serving_method == 'on_table') {
                $po->token_no = $bs->token_no + 1;
                $bs->token_no = $po->token_no;
                $bs->save();

                Session::put('pos_token_no', $po->token_no);

                $po->table_number = $request->table_no;
            }
            elseif ($request->serving_method == 'pick_up') {
                $po->pick_up_date = $request->pick_up_date;
                $po->pick_up_time = $request->pick_up_time;
            }
            elseif ($request->serving_method == 'home_delivery') {
                $po->delivery_date = $request->delivery_date;
                if ($be->delivery_date_time_status == 1) {
                    if ($request->has('delivery_time') && $request->filled('delivery_time')) {
                        $po->delivery_time_start = $tf->start;
                        $po->delivery_time_end = $tf->end;
                    }
                }

                if ($bs->postal_code == 0) {
                    if ($request->has('shipping_charge')) {
                        $shipping = ShippingCharge::findOrFail($request->shipping_charge);
                        $po->shipping_method = $shipping->title;
                        $po->shipping_charge = posShipping();
                    }
                } else {
                    $postalCode = PostalCode::findOrFail($request->postal_code);
                    $po->shipping_charge = posShipping();

                    $title = '';
                    if (!empty($postalCode->title)) {
                        $title = $postalCode->title . ' - ';
                    }
                    $po->postal_code = $title . $postalCode->postcode;
                    $po->postal_code_status = 1;
                }
            }

            $po->currency_code = $be->base_currency_text;
            $po->currency_code_position = $be->base_currency_text_position;
            $po->currency_symbol = $be->base_currency_symbol;
            $po->currency_symbol_position = $be->base_currency_symbol_position;
            $po->tax = posTax();
            $po->total = 0;
            if(!empty($request->total)){
                $po->total = $request->total;
            } else {
                $po->total = posCartSubTotal() + posTax() + posShipping();
            }
            $po->type = 'pos';
            $po->save();
            $order = $po;
            // table is now occupied
            $table->status = 0;
            $table->save();

        }
        else {
            $order = ProductOrder::where('table_number',$request->table_no)->where('completed','no')->orderBy('id','desc')->first();
        }

        $orderId = $order->id;
        // store in `customers`
        $customer = Customer::where('phone', $request->customer_phone);

        if ($customer->count() == 0) {
            $customer = new Customer;
        } else {
            $customer = $customer->first();
        }
        $customer->name = $request->customer_name;
        $customer->phone = $request->customer_phone;
        $customer->save();



        // // save cart items

        $this->saveOrderItems($order,0);

        // clear cart
        Session::forget('pos_cart');
        Session::forget('pos_shipping_charge');
        Session::forget('pos_serving_method');

        // fire sound notification
        event(new OrderPlaced($order));

        // redirect back
        Session::flash('previous_serving_method', $request->serving_method);
        Session::flash('success', 'Commande passée avec succès');
        return back();
    }

    public function saveOrderItems($order,$state) {
        $cart = Session::get('pos_cart');

        $sub_order_drink = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 0 ,'state' => $state],
            []
        );

        $sub_order_dish = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 1 ,'state' => $state],
            []
        );

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
                if($orderitem == null){
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

    public function placeOrderApi(Request $request) {

        $be = BasicExtended::first();
        $bs = BasicSetting::first();
        $table = Table::where('table_no', $request->table_no )->first();
        if ($table->status == 1) {
            // store in `product_orders`
            $po = new ProductOrder;
            $order_number = time() . "";
            $order_number = substr($order_number , 4);
            $po->order_number = $order_number;
            $po->billing_fname = $request->customer_name;
            $po->billing_number = $request->customer_phone;
            $po->serving_method = $request->serving_method;
            $po->method = $request->payment_method;
            $po->payment_status = $request->payment_status;


            if ($request->serving_method == 'on_table') {
                $po->token_no = $bs->token_no + 1;
                $bs->token_no = $po->token_no;
                $bs->save();

                Session::put('pos_token_no', $po->token_no);

                $po->table_number = $request->table_no;
            }
            elseif ($request->serving_method == 'pick_up') {
                $po->pick_up_date = $request->pick_up_date;
                $po->pick_up_time = $request->pick_up_time;
            }
            elseif ($request->serving_method == 'home_delivery') {
                $po->delivery_date = $request->delivery_date;
                if ($be->delivery_date_time_status == 1) {
                    if ($request->has('delivery_time') && $request->filled('delivery_time')) {
                        $po->delivery_time_start = $tf->start;
                        $po->delivery_time_end = $tf->end;
                    }
                }

                if ($bs->postal_code == 0) {
                    if ($request->has('shipping_charge')) {
                        $shipping = ShippingCharge::findOrFail($request->shipping_charge);
                        $po->shipping_method = $shipping->title;
                        $po->shipping_charge = posShipping();
                    }
                } else {
                    $postalCode = PostalCode::findOrFail($request->postal_code);
                    $po->shipping_charge = posShipping();

                    $title = '';
                    if (!empty($postalCode->title)) {
                        $title = $postalCode->title . ' - ';
                    }
                    $po->postal_code = $title . $postalCode->postcode;
                    $po->postal_code_status = 1;
                }
            }

            $po->currency_code = $be->base_currency_text;
            $po->currency_code_position = $be->base_currency_text_position;
            $po->currency_symbol = $be->base_currency_symbol;
            $po->currency_symbol_position = $be->base_currency_symbol_position;
            $po->tax = posTax();
            $po->total = 0;
            if(!empty($request->total)){
                $po->total = $request->total;
            } else {
                $po->total = posCartSubTotal() + posTax() + posShipping();
            }
            $po->type = 'pos';
            $po->save();
            $order = $po;
            // table is now occupied
            $table->status = 0;
            $table->save();

        }
        else {
            $order = ProductOrder::where('table_number',$request->table_no)->where('completed','no')->orderBy('id','desc')->first();
        }

        $orderId = $order->id;
        // store in `customers`
        $customer = Customer::where('phone', $request->customer_phone);

        if ($customer->count() == 0) {
            $customer = new Customer;
        } else {
            $customer = $customer->first();
        }
        $customer->name = $request->customer_name;
        $customer->phone = $request->customer_phone;
        $customer->save();



        // // save cart items

        $this->saveOrderItemsApi($order,0,$request->pos_cart);
        // return response()->json($this->saveOrderItemsApi($order,0,$request->pos_cart));exit;
        // clear cart
        Session::forget('pos_cart');
        Session::forget('pos_shipping_charge');
        Session::forget('pos_serving_method');

        // fire sound notification
        event(new OrderPlaced($order));
        // redirect back
        Session::flash('previous_serving_method', $request->serving_method);
        Session::flash('success', 'Commande passée avec succès');
        return back();
    }

    public function saveOrderItemsApi($order,$state,$cart) {
        $cart = $cart;

        $sub_order_drink = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 0 ,'state' => $state],
            []
        );

        $sub_order_dish = SubOrder::firstOrCreate(
            ['product_orders_id' =>  $order->id , 'type' => 1 ,'state' => $state],
            []
        );
        $cart_pos[]= json_decode($cart);
        if(!empty($cart_pos)) {
            foreach ($cart_pos[0] as $key => $cartItem) {
                // return response()->json($cartItem);
                $addonTotal = 0.00;
                if (!empty($cartItem->addons)) {
                    foreach ($cartItem->addons as $key => $addon) {
                        $addonTotal += (float)$addon->price;
                    }
                    $addonTotal = $addonTotal * (int)$cartItem->qty;
                }
                // $vprice = !empty($cartItem->variations) ? (float)$cartItem["variations"]["price"] * (int)$cartItem["qty"] : 0.00;
                $pprice = (float)$cartItem->product_price * (int)$cartItem->qty;


                $orderitem = OrderItem::where('product_order_id',$order->id)->where('product_id',$cartItem->id)
                                        ->where('notes',$cartItem->notes)
                                        ->where('addons',json_encode($cartItem->addons))
                                        ->where('variations',json_encode($cartItem->variations))
                                        ->first();
                if($orderitem == null){
                    $orderitem = new OrderItem();
                    $orderitem->product_order_id  =  $order->id;
                    $orderitem->product_id  =  $cartItem->id;
                    $orderitem->user_id  = Auth::check() ? Auth::user()->id : NULL;
                    $orderitem->title  = $cartItem->name;
                    $orderitem->variations  =  json_encode($cartItem->variations);
                    // $orderitem->addons  =  json_encode($cartItem["addons"]);
                    // $orderitem->notes  = $cartItem["notes"];
                    // $orderitem->variations_price  = $vprice;
                    $orderitem->addons_price = 0;
                    $orderitem->product_price  =  $pprice;
                    $orderitem->total  =  $pprice + $addonTotal;
                    $orderitem->qty  =  $cartItem->qty;  // updating global quantity
                    $orderitem->image  =  $cartItem->photo;
                    $orderitem->created_at =  Carbon::now();
                    $orderitem->save();

                }else{
                    $orderitem->qty = $orderitem->qty +  $cartItem->qty; ;
                    $orderitem->total = $orderitem->total +  $cartItem->qty*$orderitem->product_price;
                    $orderitem->save();
                }



                $category = Product::find($cartItem->id)->category;


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

                $sub_order_product->quantity = $sub_order_product->quantity + $cartItem->qty;  // updating local quantity of each sub order
                $sub_order_product->save();
            }
        }
    }

    public function shippingCharge(Request $request) {
        $sm = ServingMethod::where('value', $request->serving_method)->first();
        Session::put('pos_serving_method', $sm->name);
        Session::put('pos_shipping_charge', $request->shipping_charge);
    }
}
