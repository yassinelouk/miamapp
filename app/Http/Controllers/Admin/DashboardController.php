<?php

namespace App\Http\Controllers\Admin;
use App\Table;
use App\Models\SubOrder;
use App\Models\TableBook;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;



class DashboardController extends Controller
{
    public function dashboard(Request $request) {
      $data['table_books'] = TableBook::orderby('id','desc')->take(10)->get();

        if (Auth::user()->role_id == 10) {
            $tables = Table::where('assigned_waiter', Auth::user()->id)->get();
            $tables_number = array();
            foreach ($tables as $table) {
                array_push($tables_number, $table->table_no);
            }
        }
      $search = $request->search;
      $type = $request->orders_from;
      $servingMethod = $request->serving_method;
      $orderStatus = $request->order_status;
      $paymentStatus = $request->payment_status;
      $completed = $request->completed;
      $orderDate = $request->order_date;
      $deliveryDate = $request->delivery_date;
      $data['orders_count'] = ProductOrder::count();
      $data['orders_total'] = ProductOrder::sum('total');

      $state = $request->state != null ? intVal($request->state)  : 0;
      $type = $request->type != null ? intVal($request->type)  : 1;

      if($state == 2){
          $data['orders'] = ProductOrder::with('orderitems')
          ->where('completed', "yes")
          ->where('archived', false)
          ->orderBy('updated_at', 'DESC')->paginate(12);
          if (Auth::user()->role_id == 10) {
              foreach ($data['orders'] as $key => $order) {
                  if (!in_array($order->table_number, $tables_number) && $order->assigned_waiter != Auth::user()->id) {
                    unset($data['orders'][$key]);
                  }
              }
          }
      }else{
          $data['sub_orders'] = SubOrder::with('order','products.item.product')
          ->where('sub_orders.state', $state)
          ->where('sub_orders.type', $type)
          ->orderBy('updated_at', 'DESC')->paginate(12);
          if (Auth::user()->role_id == 10) {
              foreach ($data['sub_orders'] as $key => $sub_order) {
                  if (!in_array($sub_order->order->table_number, $tables_number)) {
                    unset($data['sub_orders'][$key]);
                  }
              }
          }
      }
       $data['state'] = $state."";
       $data['type'] = $type."";
       $data['sub_orders_count'] = 0;
       $data['tables'] = Table::orderBy('table_no')->paginate(12); // available tables
    //    return response()->json($data);
      return view('admin.dashboard',$data);
    }

    public function createSessionVariable(Request $request) {
        $order_number = $request->var_name;
        $amount = $request->amount;
        Session::put("#" . $order_number, $amount);
        Session::save();
        return json_encode(array(
            "success" => "amount successfully updated"
        ));
    }
}
