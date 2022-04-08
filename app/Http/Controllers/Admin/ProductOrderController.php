<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Str;
use App\Table;
use Validator;
use Carbon\Carbon;
use App\Models\Language;
use App\Models\SubOrder;

use App\Models\OrderItem;
use App\Models\OrderTime;
use App\Models\TimeFrame;
use App\Models\BasicSetting;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use App\Models\BasicExtended;
use App\Models\ServingMethod;
use App\Models\OfflineGateway;
use App\Http\Helpers\MegaMailer;
use App\Models\SubOrdersProducts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ProductOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $type = $request->orders_from;
        $servingMethod = $request->serving_method;
        $orderStatus = $request->order_status;
        $paymentStatus = $request->payment_status;
        $completed = $request->completed;
        $orderDate = $request->order_date;
        $deliveryDate = $request->delivery_date;

        $data['orders'] = ProductOrder::when($search, function ($query, $search) {
            return $query->where('order_number', 'LIKE','%'.$search.'%');
        })->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })->when($servingMethod, function ($query, $servingMethod) {
            return $query->where('serving_method', $servingMethod);
        })->when($orderStatus, function ($query, $orderStatus) {
            return $query->where('order_status', $orderStatus);
        })->when($paymentStatus, function ($query, $paymentStatus) {
            return $query->where('payment_status', $paymentStatus);
        })->when($completed, function ($query, $completed) {
            return $query->where('completed', $completed);
        })->when($orderDate, function ($query, $orderDate) {
            return $query->whereDate('created_at', Carbon::parse($orderDate));
        })->when($deliveryDate, function ($query, $deliveryDate) {
            return $query->where('delivery_date', $deliveryDate);
        })
        ->orderBy('id', 'DESC')->paginate(10);

        return view('admin.product.order.index', $data);
    }

    public function indexApi(Request $request)
    {
        $search = $request->search;
        $type = $request->orders_from;
        $servingMethod = $request->serving_method;
        $orderStatus = $request->order_status;
        $paymentStatus = $request->payment_status;
        $completed = $request->completed;
        $orderDate = $request->order_date;
        $deliveryDate = $request->delivery_date;

        $data['orders'] = ProductOrder::when($search, function ($query, $search) {
            return $query->where('order_number', 'LIKE','%'.$search.'%');
        })->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })->when($servingMethod, function ($query, $servingMethod) {
            return $query->where('serving_method', $servingMethod);
        })->when($orderStatus, function ($query, $orderStatus) {
            return $query->where('order_status', $orderStatus);
        })->when($paymentStatus, function ($query, $paymentStatus) {
            return $query->where('payment_status', $paymentStatus);
        })->when($completed, function ($query, $completed) {
            return $query->where('completed', $completed);
        })->when($orderDate, function ($query, $orderDate) {
            return $query->whereDate('created_at', Carbon::parse($orderDate));
        })->when($deliveryDate, function ($query, $deliveryDate) {
            return $query->where('delivery_date', $deliveryDate);
        })
        ->orderBy('id', 'DESC')->paginate(10);

        return response()->json($data['orders']);

    }

    public function settings() {
        return view('admin.product.order.settings');
    }

    public function resetToken(Request $request) {
        $bss = BasicSetting::all();
        foreach ($bss as $key => $bs) {
            $bs->token_no_start = $request->token_no;
            $bs->token_no = $request->token_no - 1;
            $bs->save();
        }

        Session::flash('success', __('admin_panel.token_reset'));
        return back();
    }

    public function updateSettings(Request $request) {
        $bss = BasicSetting::all();
        foreach ($bss as $key => $bs) {
            $bs->postal_code = $request->postal_code;
            $bs->save();
        }

        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->delivery_date_time_status = $request->delivery_date_time_status;
            $be->delivery_date_time_required = $request->delivery_date_time_required;
            $be->save();
        }
        Session::flash('success', trans_choice('admin_panel.update',2, ['Item' => __('Settings')]));
        return back();
    }

    public function deleteProductItem(Request $request){

        $data = $request->all();
        $suborderitem = SubOrdersProducts::find($data['id']);
        $orderItem = OrderItem::find($suborderitem->orders_item_id);

        if($orderItem->qty > $suborderitem->quantity ){
            $orderItem->qty =  $orderItem->qty  - $suborderitem->quantity;
            $suborderitem->delete();
            $orderItem->save();

        }else{

            $subOrder = SubOrder::find($suborderitem->sub_order_id);

            $productOrder = ProductOrder::find($orderItem->product_order_id);

            $suborderitem->delete();
            $orderItem->delete();

            if(count($productOrder->orderitems) == 0){
                foreach($productOrder->suborders as $suborder){
                    $suborder->delete();
                }

                            // update disponibility
            $table = Table::where('table_no', $productOrder->table_number)->first();
            $table->status = 1 ;
            $table->client_session_id = null ;
            $table->save();
            $productOrder->delete();

            }


        }

        return redirect()->back();
    }

    public function updateSubOrderState($sub_order_id,$state){
        $so = SubOrder::with('products')->find($sub_order_id);
        $order = $so->order ;
        Session::forget("#" . $order->order_number);
        $expectedSubOrder = SubOrder::with('products.item')->where('product_orders_id',$so->product_orders_id)
        ->where('type',$so->type)
            ->where('state',$state)
            ->first();

        if($expectedSubOrder == null){
            // place this sub order on his new state , cuz no suborder wth same table_no is active
            $so->state = $state;
            $so->save();
        }else{
            // add sub order products to each others
            $expected_order = $expectedSubOrder->order;
            $products = $so->products ;

            foreach ($products as $new_sub_order_product){

                $item_exist = false ;
                foreach ($expectedSubOrder->products as $existing_product){
                    $existing_item = $existing_product->item;
                    if (
                        strcmp($existing_item->product_id.'', $new_sub_order_product->item->product_id.'')  == 0
                        &&  strcmp($existing_item->notes.'', $new_sub_order_product->item->notes.'')  == 0
                    ){
                        // the existing sub order contain the same item with same note
                        $item_exist = true ;
                        //  we add quantities
                        $existing_product->quantity = $existing_product->quantity + $new_sub_order_product->quantity ;
                        $existing_product->save();
                        $new_sub_order_product->delete();
                    }
                }

                if(!$item_exist){
                    // transfert product to new sub order
                    $new_sub_order_product->sub_order_id = $expectedSubOrder->id;
                    $new_sub_order_product->save();
                }

            }
            $so->delete();
        }



        if($state == 2){

            $fileName = Str::random(4) . time() . '.pdf';
            $path = 'assets/front/invoices/product/' . $fileName;
            $order = ProductOrder::find($so->product_orders_id);
            $data['order']  = $order;
            PDF::loadView('pdf.product', $data)->save($path);

            $table = Table::where('table_no',$order->table_number)->first();
            ProductOrder::where('id', $order->id)->update([
                'invoice_number' => $fileName,
                'completed' => 'yes',
                'assigned_waiter' => $table->assigned_waiter
            ]);

           // finalize all sub orders todo: delete theme instead
            SubOrder::where('product_orders_id', $order->id)->update([
                'state' => $state
            ]);

            // update disponibility
            $table->status = 1 ;
            $table->client_session_id = null ;
            $table->assigned_waiter = null;
            $table->save();
        }

        return redirect()->back(); // to dashboard
    }

    public function updateSubOrderStateApi($sub_order_id,$state){
        $so = SubOrder::with('products')->find($sub_order_id);
        $order = $so->order ;
        Session::forget("#" . $order->order_number);
        $expectedSubOrder = SubOrder::with('products.item')->where('product_orders_id',$so->product_orders_id)
        ->where('type',$so->type)
            ->where('state',$state)
            ->first();

        if($expectedSubOrder == null){
            // place this sub order on his new state , cuz no suborder wth same table_no is active
            $so->state = $state;
            $so->save();
        }else{
            // add sub order products to each others
            $expected_order = $expectedSubOrder->order;
            $products = $so->products ;

            foreach ($products as $new_sub_order_product){

                $item_exist = false ;
                foreach ($expectedSubOrder->products as $existing_product){
                    $existing_item = $existing_product->item;
                    if (
                        strcmp($existing_item->product_id.'', $new_sub_order_product->item->product_id.'')  == 0
                        &&  strcmp($existing_item->notes.'', $new_sub_order_product->item->notes.'')  == 0
                    ){
                        // the existing sub order contain the same item with same note
                        $item_exist = true ;
                        //  we add quantities
                        $existing_product->quantity = $existing_product->quantity + $new_sub_order_product->quantity ;
                        $existing_product->save();
                        $new_sub_order_product->delete();
                    }
                }

                if(!$item_exist){
                    // transfert product to new sub order
                    $new_sub_order_product->sub_order_id = $expectedSubOrder->id;
                    $new_sub_order_product->save();
                }

            }
            $so->delete();
        }



        if($state == 2){

            $fileName = Str::random(4) . time() . '.pdf';
            $path = 'assets/front/invoices/product/' . $fileName;
            $order = ProductOrder::find($so->product_orders_id);
            $data['order']  = $order;
            PDF::loadView('pdf.product', $data)->save($path);

            $table = Table::where('table_no',$order->table_number)->first();
            ProductOrder::where('id', $order->id)->update([
                'invoice_number' => $fileName,
                'completed' => 'yes',
                'assigned_waiter' => $table->assigned_waiter
            ]);

           // finalize all sub orders todo: delete theme instead
            SubOrder::where('product_orders_id', $order->id)->update([
                'state' => $state
            ]);

            // update disponibility
            $table->status = 1 ;
            $table->client_session_id = null ;
            $table->assigned_waiter = null;
            $table->save();
        }

        return response()->json('success');
    }


    public function updateOrderTable(Request $request){
        // current order that need to be changed
        $order = ProductOrder::find($request->order_id);
        $sub_order = SubOrder::with('products.item')->find($request->sub_order_id);
        $oldTableNumber = $order->table_number ;

        $expected_order = ProductOrder::where('table_number',$request->table_number)->where('completed','no')->orderBy('id','desc')->first();

        // if there is a current order in this table
        if($expected_order){

            // we add products to each others
            $expectedSubOrder = SubOrder::with('products.item')->where('product_orders_id',$expected_order->id)
                ->where('type',$sub_order->type)
                ->where('state',$sub_order->state)
                ->first();


            // if there is a sub order with same new table_no and state and type
            if($expectedSubOrder !== null){

                // add sub order products to each others
                $products = $sub_order->products ;
                foreach ($products as $new_sub_order_product){

                    $item_exist = false ;
                    foreach ($expectedSubOrder->products as $existing_product){
                        $existing_item = $existing_product->item;
                        if (
                            strcmp($existing_item->product_id.'', $new_sub_order_product->item->product_id.'')  == 0
                            &&  strcmp($existing_item->notes.'', $new_sub_order_product->item->notes.'')  == 0
                        ){
                            // the existing sub order contain the same item with same note
                            $item_exist = true ;
                            //  we add quantities
                            $existing_product->quantity = $existing_product->quantity + $new_sub_order_product->quantity ;
                            $existing_product->save();
                            $new_sub_order_product->delete();
                        }
                    }

                    if(!$item_exist){
                        // transfert product to new sub order
                        $new_sub_order_product->sub_order_id = $expectedSubOrder->id;
                        $new_sub_order_product->save();
                    }

                }

                foreach($order->suborders as $so){
                     $so->delete();
                }
               // $sub_order->delete();
               $order->delete();

            }else{
                // place this sub order on his new order , cuz no suborder wth same table_no is active here
                $sub_order->product_orders_id = $expected_order->id;
                $sub_order->save();
            }

        }else{

           // we don't have a current order in this table
                $order->table_number = $request->table_number;
                $order->save();
        }


        // update disponibility old table
        $o_table = Table::where('table_no',$oldTableNumber)->first();
        if($o_table){
            $o_table->status = 1 ;
            $o_table->client_session_id = null ;
            $o_table->save();
        }



        // update disponibility new table
        $table = Table::where('table_no',$request->table_number)->first();
        $table->status = 0 ;
        $table->client_session_id = session()->getId() ;
        $table->save();

        return redirect()->back(); // to dashboard
    }

    public function transferProducts(Request $request){
        $datas = $request->all();
        $current_sub_order = SubOrder::with('order','products')->find($datas['sub_order_id']);
        $sub_orders_products_ids = array();
        foreach ($datas as $key => $data){
            if(str_starts_with( $key,  "sub-order-product")){
                $id = explode("sub-order-product-",$key)[1];
                array_push($sub_orders_products_ids,$id);
            }
        }

        if(empty($sub_orders_products_ids)) {
            return redirect()->back();
        }

        $next_table = Table::where('table_no',$datas['table_number'])->first();


        if($next_table->status == 0){
            // table already have an order
            $expected_order = ProductOrder::where('table_number',$datas['table_number'])->where('completed','no')->orderBy('id','desc')->first();
            $expected_sub_order = SubOrder::where('type',$current_sub_order->type)->where('state',$current_sub_order->state)->where('product_orders_id',$expected_order->id)->orderBy('id','desc')->first();

            if($expected_sub_order == null){
                            $expected_sub_order = SubOrder::updateOrCreate(
                                ['product_orders_id' => $expected_order->id , 'type' => $current_sub_order->type , 'state' => $current_sub_order->state],
                                []
                                );
            }

            // we add products to each others
            foreach ($sub_orders_products_ids as $sub_order_product_id){
                $sub_order_product = SubOrdersProducts::where('id',$sub_order_product_id)->with('item')->first();
                $item = OrderItem::find( $sub_order_product->item->id);

                $expected_orderitem = OrderItem::where('product_order_id',$expected_order->id)->where('product_id',$sub_order_product->item->product_id)
                    ->where('notes',$sub_order_product->item->notes)->first();

                if($expected_orderitem){
                    // there is a product in expected sub order with same comment
                    $expected_orderitem->qty = $expected_orderitem->qty +  $sub_order_product->item->qty;
                    $expected_orderitem->save();
                    // delete old sub order from list
                    $sub_order_product->delete();
                    $item->delete();
                }else{

                    $sub_order_product->sub_order_id = $expected_sub_order->id;
                    $item->product_order_id = $expected_order->id;
                    $item->save();
                    $sub_order_product->save();
                }

            }


        }else{
            // we create a new order with selected products
            $order = ProductOrder::find($datas['order_id']);
            $newOrder = $order->replicate();
            $newOrder->table_number = $datas['table_number'];
            $newOrder->save();
            $suborder_dish = SubOrder::updateOrCreate(
                ['product_orders_id' => $newOrder->id , 'type' => 1 , 'state' => $current_sub_order->state],
                []
            );

            $suborder_drink = SubOrder::updateOrCreate(
                ['product_orders_id' => $newOrder->id , 'type' => 0 , 'state' => $current_sub_order->state],
                []
            );

            // we add products to each others
            foreach ($sub_orders_products_ids as $sub_order_product_id){

                $sub_order_product = SubOrdersProducts::where('id',$sub_order_product_id)->with('item.product.category')->first();
                $item = OrderItem::find( $sub_order_product->item->id);
                $item->product_order_id = $newOrder->id;
                $item->save();
                $categoryType = $sub_order_product->item->product->category->type ;
                if($categoryType == 0){
                    $sub_order_product->sub_order_id = $suborder_drink->id;
                }
                else{
                    $sub_order_product->sub_order_id = $suborder_dish->id;
                }

                $sub_order_product->save();


            }

                // table is now occuped
                $next_table->status = 0 ;
                $next_table->client_session_id = session()->getId() ;
                $next_table->save();


        }

                // old table is now free if it has no product
                $current_sub_order = SubOrder::with('order','products')->find($datas['sub_order_id']);
                if(count($current_sub_order->products) == 0){
                    $old_table = Table::where('table_no',$current_sub_order->order->table_number)->first();
                    $old_table->status = 1 ;
                    $old_table->client_session_id = null;
                    $old_table->save();
                }

        return redirect()->back(); // to dashboard
    }

    public function transferProductsApi(Request $request){
        $datas = $request->all();
        $current_sub_order = SubOrder::with('order','products')->find($datas['sub_order_id']);
        $sub_orders_products_ids = array();
        foreach ($datas as $key => $data){
            if(str_starts_with( $key,  "sub-order-product")){
                $id = explode("sub-order-product-",$key)[1];
                array_push($sub_orders_products_ids,$id);
            }
        }

        if(empty($sub_orders_products_ids)) {
            return redirect()->back();
        }

        $next_table = Table::where('table_no',$datas['table_number'])->first();


        if($next_table->status == 0){
            // table already have an order
            $expected_order = ProductOrder::where('table_number',$datas['table_number'])->where('completed','no')->orderBy('id','desc')->first();
            $expected_sub_order = SubOrder::where('type',$current_sub_order->type)->where('state',$current_sub_order->state)->where('product_orders_id',$expected_order->id)->orderBy('id','desc')->first();

            if($expected_sub_order == null){
                            $expected_sub_order = SubOrder::updateOrCreate(
                                ['product_orders_id' => $expected_order->id , 'type' => $current_sub_order->type , 'state' => $current_sub_order->state],
                                []
                                );
            }

            // we add products to each others
            foreach ($sub_orders_products_ids as $sub_order_product_id){
                $sub_order_product = SubOrdersProducts::where('id',$sub_order_product_id)->with('item')->first();
                $item = OrderItem::find( $sub_order_product->item->id);

                $expected_orderitem = OrderItem::where('product_order_id',$expected_order->id)->where('product_id',$sub_order_product->item->product_id)
                    ->where('notes',$sub_order_product->item->notes)->first();

                if($expected_orderitem){
                    // there is a product in expected sub order with same comment
                    $expected_orderitem->qty = $expected_orderitem->qty +  $sub_order_product->item->qty;
                    $expected_orderitem->save();
                    // delete old sub order from list
                    $sub_order_product->delete();
                    $item->delete();
                }else{

                    $sub_order_product->sub_order_id = $expected_sub_order->id;
                    $item->product_order_id = $expected_order->id;
                    $item->save();
                    $sub_order_product->save();
                }

            }


        }else{
            // we create a new order with selected products
            $order = ProductOrder::find($datas['order_id']);
            $newOrder = $order->replicate();
            $newOrder->table_number = $datas['table_number'];
            $newOrder->save();
            $suborder_dish = SubOrder::updateOrCreate(
                ['product_orders_id' => $newOrder->id , 'type' => 1 , 'state' => $current_sub_order->state],
                []
            );

            $suborder_drink = SubOrder::updateOrCreate(
                ['product_orders_id' => $newOrder->id , 'type' => 0 , 'state' => $current_sub_order->state],
                []
            );

            // we add products to each others
            foreach ($sub_orders_products_ids as $sub_order_product_id){

                $sub_order_product = SubOrdersProducts::where('id',$sub_order_product_id)->with('item.product.category')->first();
                $item = OrderItem::find( $sub_order_product->item->id);
                $item->product_order_id = $newOrder->id;
                $item->save();
                $categoryType = $sub_order_product->item->product->category->type ;
                if($categoryType == 0){
                    $sub_order_product->sub_order_id = $suborder_drink->id;
                }
                else{
                    $sub_order_product->sub_order_id = $suborder_dish->id;
                }

                $sub_order_product->save();


            }

                // table is now occuped
                $next_table->status = 0 ;
                $next_table->client_session_id = session()->getId() ;
                $next_table->save();


        }

                // old table is now free if it has no product
                $current_sub_order = SubOrder::with('order','products')->find($datas['sub_order_id']);
                if(count($current_sub_order->products) == 0){
                    $old_table = Table::where('table_no',$current_sub_order->order->table_number)->first();
                    $old_table->status = 1 ;
                    $old_table->client_session_id = null;
                    $old_table->save();
                }

        return redirect()->back(); // to dashboard
    }


    public function status(Request $request)
    {

        $po = ProductOrder::find($request->order_id);
        $po->order_status = $request->order_status;
        $po->save();

        $bs = BasicSetting::first();

        $status = $request->order_status;
        $servingMethod = $po->serving_method;
        $templateType = 'pending';

        if ($status == 'received') {
            $templateType = 'order_received';
        } elseif ($status == 'preparing') {
            $templateType = 'order_preparing';
        } elseif ($status == 'ready_to_pick_up' && $servingMethod == 'home_delivery') {
            $templateType = 'order_ready_to_pickup';
        } elseif ($status == 'ready_to_pick_up' && $servingMethod == 'pick_up') {
            $templateType = 'order_ready_to_pickup_pick_up';
        } elseif ($status == 'picked_up' && $servingMethod == 'home_delivery') {
            $templateType = 'order_pickedup';
        } elseif ($status == 'picked_up' && $servingMethod == 'pick_up') {
            $templateType = 'order_pickedup_pick_up';
        } elseif ($status == 'delivered') {
            $templateType = 'order_delivered';
        } elseif ($status == 'cancelled') {
            $templateType = 'order_cancelled';
        } elseif ($status == 'served') {
            $templateType = 'order_served';
        } elseif ($status == 'ready_to_serve') {
            $templateType = 'order_ready_to_serve';
        } else {
            Session::flash('success', 'Order status changed successfully!');
            return back();
        }

        // $mailer = new MegaMailer();
        // $data = [
        //     'toMail' => $po->billing_email,
        //     'toName' => $po->billing_fname,
        //     'customer_name' => $po->billing_fname,
        //     'order_number' => $po->order_number,
        //     'order_link' => "<a href='" . route('user-orders-details', $po->id) . "'>" . route('user-orders-details', $po->id) . "</a>",
        //     'website_title' => $bs->website_title,
        //     'templateType' => $templateType,
        //     'type' => 'foodOrderStatus'
        // ];
        // $mailer->mailFromAdmin($data);

        Session::flash('success', 'Order status changed !');
        return back();
    }

    public function completed(Request $request) {
        $po = ProductOrder::find($request->order_id);
        $po->completed = $request->completed;
        $po->save();
        Session::flash('success', 'Statut complet modifié !');
        return back();
    }

    public function payment(Request $request) {
        $po = ProductOrder::find($request->order_id);
        $po->payment_status = $request->payment_status;
        $po->save();
        Session::flash('success', 'Statut de paiement modifié !');
        return back();
    }

    public function details($id)
    {
        $order = ProductOrder::findOrFail($id);
        return view('admin.product.order.details', compact('order'));
    }

    public function printReceipt($id)
    {
        $order = ProductOrder::findOrFail($id);
        return view('admin.partials.customers-receipt', compact('order'));
    }

    public function hideOrder($id)
    {
        $order = ProductOrder::findOrFail($id);
        $order->archived = true ;
        $order->save();
        return redirect()->back();
    }




    public function bulkOrderDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $order = ProductOrder::findOrFail($id);
            $is_finished = false;

            @unlink('assets/front/invoices/product/' . $order->invoice_number);
            @unlink('assets/front/receipt/' . $order->receipt);
            foreach ($order->orderitems as $item) {
                $item->delete();
            }

            foreach ($order->suborders as $item) {
                if($item->state == 2)
                    $is_finished = true;
                foreach($item->products as $product_item){
                    $product_item->delete();
                }
                $item->delete();
            }

            if( $is_finished == false && $order->table_number != null){
            $table = Table::where( 'table_no',$order->table_number)->first();
            $table->status = 1;
            $table->save();
            }


            $order->delete();
        }

        Session::flash('success',trans_choice('admin_panel.delete',count($ids),['Item' => __('Order')]));
        return "success";
    }

    public function orderDelete(Request $request)
    {
        $order = ProductOrder::findOrFail($request->order_id);
        @unlink('assets/front/invoices/product/' . $order->invoice_number);
        foreach ($order->orderitems as $item) {
            $item->delete();
        }
        $is_finished = false;

        foreach ($order->suborders as $item) {
            if($item->state == 2)
                $is_finished = true;
            foreach($item->products as $product_item){
                $product_item->delete();
            }
            $item->delete();
        }

        if( $is_finished == false && $order->table_number != null ){

            $table = Table::where( 'table_no',$order->table_number)->first();

            $table->status = 1;
            $table->save();
        }
        $order->delete();
        Session::flash('success',trans_choice('admin_panel.delete',count($order->orderitems),['Item' => __('Product')]));
        return back();
    }

    public function orderDeleteApi($id)
    {
        $order = ProductOrder::findOrFail($id);
        @unlink('assets/front/invoices/product/' . $order->invoice_number);
        foreach ($order->orderitems as $item) {
            $item->delete();
        }
        $is_finished = false;

        foreach ($order->suborders as $item) {
            if($item->state == 2)
                $is_finished = true;
            foreach($item->products as $product_item){
                $product_item->delete();
            }
            $item->delete();
        }

        if( $is_finished == false && $order->table_number != null ){

            $table = Table::where( 'table_no',$order->table_number)->first();

            $table->status = 1;
            $table->save();
        }
        $order->delete();
        Session::flash('success',trans_choice('admin_panel.delete',count($order->orderitems),['Item' => __('Product')]));
        return back();
    }

    public function qrPrint(Request $request) {
        $order = ProductOrder::find($request->order_id);

        if ($order->method == 'paypal') {
            $url = route('product.paypal.apiRequest', $request->order_id);
        } elseif ($order->method == 'mollie') {
            $url = route('product.mollie.apiRequest', $request->order_id);
        }

        $fileName = uniqid() . '.svg';
        \QrCode::size(150)
        ->color(0,0,0)
        ->format('svg')
        ->generate($url, 'assets/front/img/' . $fileName);

        return url('assets/front/img/' . $fileName);
    }

    public function servingMethods() {
        $servingMethods = ServingMethod::all();
        $data['servingMethods'] = $servingMethods;
        $data['ogateways'] = OfflineGateway::where('status', 1)->get();

        return view('admin.product.order.serving_methods.index', $data);
    }

    public function servingMethodStatus(Request $request) {
        // return $request;
        $website = ServingMethod::where('website_menu', 1)->count();
        $qr = ServingMethod::where('qr_menu', 1)->count();

        if ($website == 1 && $request->website_menu == 0) {
            Session::flash('warning', 'Minimum 1 serving method must be activated for Website Menu.');
            return back();
        }
        if ($qr == 1 && $request->qr_menu == 0) {
            Session::flash('warning', 'Minimum 1 serving method must be activated for QR Menu.');
            return back();
        }

        $sm = ServingMethod::find($request->serving_method);
        $sm->website_menu = $request->website_menu;
        $sm->qr_menu = $request->qr_menu;
        $sm->pos = $request->pos;
        $sm->save();

        Session::flash('success', __('admin_panel.update', ['Item' => __('Status')]));
        return back();
    }

    public function servingMethodGateways(Request $request) {
        $sm = ServingMethod::find($request->serving_method);
        $sm->gateways = json_encode($request->gateways);
        $sm->save();

        Session::flash('success', 'Gateways status updated successfully!');
        return back();
    }

    public function qrPayment(Request $request) {
        $sm = ServingMethod::find($request->serving_method);
        $sm->qr_payment = $request->qr_payment;
        $sm->save();

        Session::flash('success', 'QR scan payment status updated successfully!');
        return back();
    }

    public function servingMethodUpdate(Request $request) {
        $sm = ServingMethod::find($request->serving_method);
        $sm->serial_number = $request->serial_number;
        $sm->note = $request->note;
        $sm->save();

        Session::flash('success', 'Updated successfully!');
        return back();
    }

    public function ordertime() {
        $data['ordertimes'] = OrderTime::all();
        return view('admin.product.order.order-time', $data);
    }

    public function updateOrdertime(Request $request) {
        $ids = $request->ids;
        $start = $request->start_time;
        $end = $request->end_time;


        for ($i=0; $i < count($ids); $i++) {
            $ot = OrderTime::where('id', $ids[$i])->first();
            $ot->start_time = $start[$i];
            $ot->end_time = $end[$i];
            $ot->save();
        }

        session()->flash('success', __('Order times updated successfully'));
        return back();
    }

    public function deliverytime() {
        return view('admin.product.order.delivery_time.index');
    }

    public function timeframes(Request $request) {
        $data['timeframes'] = TimeFrame::where('day', $request->day)->get();
        return view('admin.product.order.delivery_time.timeframes', $data);
    }

    public function orderTimeStore(Request $request) {
        $rules = [
            'start' => 'required',
            'end' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $tf = new OrderTime;
        $tf->day = $request->day;
        $tf->start_time = $request->start;
        $tf->end_time = $request->end;
        $tf->save();

        Session::flash('success', 'Time frame added successfully!');
        return "success";
    }

    public function OrderTimeDelete(Request $request)
    {
        $id = $request->ordertf_id;

        $tf = OrderTime::findOrFail($id);
        $tf->delete();

        Session::flash('success', 'Time frame deleted successfully!');

    }
    public function timeframeStore(Request $request) {
        $rules = [
            'start' => 'required',
            'end' => 'required',
            'max_orders' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $tf = new TimeFrame;
        $tf->day = $request->day;
        $tf->start = $request->start;
        $tf->end = $request->end;
        $tf->max_orders = $request->max_orders;
        $tf->save();

        Session::flash('success', 'Time frame added successfully!');
        return "success";
    }

    public function timeframeUpdate(Request $request) {
        $rules = [
            'start' => 'required',
            'end' => 'required',
            'max_orders' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $tf = TimeFrame::find($request->timeframe_id);
        $tf->start = $request->start;
        $tf->end = $request->end;
        $tf->max_orders = $request->max_orders;
        $tf->save();

        Session::flash('success', 'Time frame updated successfully!');
        return "success";
    }

    public function timeframeDelete(Request $request)
    {

        $tf = TimeFrame::findOrFail($request->timeframe_id);
        $tf->delete();

        Session::flash('success', 'Time frame deleted successfully!');
        return back();
    }

    public function deliveryStatus(Request $request) {
        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->delivery_date_time_status = $request->delivery_date_time_status;
            $be->delivery_date_time_required = $request->delivery_date_time_required;
            $be->save();
        }

        Session::flash('success', 'Status updated successfully!');
        return back();
    }

    public function orderclose(Request $request) {
        $rules = [
            'order_close' => 'required',
        ];

        $messages = [];

        if ($request->order_close == 1) {
            $rules['order_close_message'] = 'required|max:255';
            $messages['order_close_message.required'] = 'The message field is required';
            $messages['order_close_message.max'] = 'The message field cannot contain more than 255 characters';
        }

        $request->validate($rules, $messages);

        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->order_close = $request->order_close;
            if ($request->order_close == 1) {
                $be->order_close_message = $request->order_close_message;
            }
            $be->save();
        }

        Session::flash('success', 'Status updated successfully!');
        return back();
    }

}
