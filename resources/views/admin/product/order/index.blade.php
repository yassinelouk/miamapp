
@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">
      {{__('Orders')}}
    </h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{__('My Orders')}}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">
          {{__('Orders')}}
        </a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="card">
        <div class="card-header">
            <form id="searchForm" action="{{route('admin.product.orders')}}" method="GET" onsubmit="document.getElementById('searchForm').submit()">
                <div class="row no-gutters">
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Ordered From')}}</label>
                            <select name="orders_from" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                <option value="" {{empty(request()->input('orders_from')) ? 'selected' : ''}}>{{__('All')}}</option>
                                <option value="website" {{request()->input('orders_from') == 'website' ? 'selected' : ''}}>{{__('Website Menu')}}</option>
                                <option value="qr" {{request()->input('orders_from') == 'qr' ? 'selected' : ''}}>{{__('Qr Menu')}}</option>
                                <option value="pos" {{request()->input('orders_from') == 'pos' ? 'selected' : ''}}>POS</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Serving Method')}}</label>
                            <select name="serving_method" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                <option value="" {{empty(request()->input('orders_from')) ? 'selected' : ''}}>{{__('All')}}</option>
                                <option value="on_table" {{request()->input('serving_method') == 'on_table' ? 'selected' : ''}}>{{__('On Table')}}</option>
                                <option value="pick_up" {{request()->input('serving_method') == 'pick_up' ? 'selected' : ''}}>{{__('Pick Up')}}</option>
                                <option value="home_delivery" {{request()->input('serving_method') == 'home_delivery' ? 'selected' : ''}}>{{__('Home Delivery')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Orders')}}</label>
                            <select name="order_status" id="" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                <option value="" {{empty(request()->input('order_status')) ? 'selected' : ''}}>{{__('All')}}</option>
                                <option value="pending" {{request()->input('order_status') == 'pending' ? 'selected' : ''}}>{{__('Pending')}}</option>
                                <option value="received" {{request()->input('order_status') == 'received' ? 'selected' : ''}}>{{__('Received')}}</option>
                                <option value="preparing" {{request()->input('order_status') == 'preparing' ? 'selected' : ''}}>{{__('Preparing')}}</option>

                                @if (empty(request()->input('serving_method')) || request()->input('serving_method') == 'home_delivery' || request()->input('serving_method') == 'pick_up')
                                <option value="ready_to_pick_up" {{request()->input('order_status') == 'ready_to_pick_up' ? 'selected' : ''}}>{{__('Ready to pick up')}}</option>
                                <option value="picked_up" {{request()->input('order_status') == 'picked_up' ? 'selected' : ''}}>{{__('Picked up')}}</option>
                                @endif

                                @if (empty(request()->input('serving_method')) || request()->input('serving_method') == 'home_delivery')
                                <option value="delivered" {{request()->input('order_status') == 'delivered' ? 'selected' : ''}}>{{__('Delivered')}}</option>
                                @endif

                                @if (empty(request()->input('serving_method')) || request()->input('serving_method') == 'on_table')
                                <option value="ready_to_serve" {{request()->input('order_status') == 'ready_to_serve' ? 'selected' : ''}}>{{__('Ready to Serve')}}</option>
                                <option value="served" {{request()->input('order_status') == 'served' ? 'selected' : ''}}>{{__('Served')}}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Payment Status')}}</label>
                            <select name="payment_status" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                <option value="">{{__('All')}}</option>
                                <option value="Pending" {{request()->input('payment_status') == 'Pending' ? 'selected' : ''}}>{{__('Pending')}}</option>
                                <option value="Completed" {{request()->input('payment_status') == 'Completed' ? 'selected' : ''}}>{{__('Complete')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Complete')}}</label>
                            <select name="completed" class="form-control" onchange="document.getElementById('searchForm').submit()">
                                <option value="">{{__('All')}}</option>
                                <option value="yes" {{request()->input('completed') == 'yes' ? 'selected' : ''}}>{{__('Yes')}}</option>
                                <option value="no" {{request()->input('completed') == 'no' ? 'selected' : ''}}>{{__('No')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Order Date')}}</label>
                            <input class="form-control datepicker" type="text" name="order_date" onchange="document.getElementById('searchForm').submit()" autocomplete="off" value="{{request()->input('order_date')}}">
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Delivery Date')}}</label>
                            <input class="form-control datepicker" type="text" name="delivery_date" onchange="document.getElementById('searchForm').submit()" autocomplete="off" value="{{request()->input('delivery_date')}}">
                        </div>
                    </div>
                    <div class="px-1">
                        <div class="form-group px-0">
                            <label for="">{{__('Order Number')}}</label>
                            <input class="form-control" type="text" name="search" onfocusout="document.getElementById('searchForm').submit()" value="{{request()->input('search') ? request()->input('search') : '' }}">
                        </div>
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type="button" class="btn btn-danger btn-sm ml-4 d-none bulk-delete" data-href="{{route('admin.product.order.bulk.delete')}}"><i class="flaticon-interface-5"></i> {{__('Delete')}}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($orders) == 0)
                <h3 class="text-center">{{__('NO ORDER FOUND')}}</h3>
              @else
                <div id="refreshOrder">
                    <div class="table-responsive">
                      <table class="table table-striped mt-3">
                        <thead>
                          <tr>
                            <th scope="col">
                                <input type="checkbox" class="bulk-check" data-val="all">
                            </th>

                            <th scope="col">{{__('Order Number')}}</th>
                            <th scope="col">{{__('Serving Method')}}</th>
                            <th scope="col">{{__('Payment Status')}}</th>
                            <th scope="col">{{__('Status')}}</th>
                            <th scope="col">{{__('Completed')}}</th>
                            <th scope="col">{{__('Payment Gateways')}}</th>
                            <th scope="col">{{__('Time')}}</th>
                            <th scope="col">{{__('Action')}}</th>
                          </tr>
                        </thead>

                        <tbody>
                          @foreach ($orders as $key => $order)
                            <tr>
                              <td>
                                <input type="checkbox" class="bulk-check" data-val="{{$order->id}}">
                              </td>
                              <td>{{$order->order_number}}</td>
                              <td class="text-capitalize">
                                  @if ($order->serving_method == 'on_table')
                                      {{__('On Table') . " " . $order->table_number}}
                                  @elseif ($order->serving_method == 'home_delivery')
                                      {{__('Home Delivery')}}
                                  @elseif ($order->serving_method == 'pick_up')
                                      {{__('Pick Up')}}
                                  @endif
                              </td>
                              <td>
                                @if ($order->type == 'pos' || $order->gateway_type == 'offline')
                                    <form id="paymentForm{{$order->id}}" class="d-inline-block" action="{{route('admin.product.order.payment')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{$order->id}}">
                                        <select class="form-control form-control-sm w-auto form-rounded text-light
                                            @if ($order->payment_status == 'Pending')
                                                bg-warning
                                            @elseif ($order->payment_status == 'Completed')
                                                bg-success
                                            @endif
                                        " name="payment_status" onchange="document.getElementById('paymentForm{{$order->id}}').submit();">
                                                <option value="Pending" {{$order->payment_status == 'Pending' ? 'selected' : ''}}>{{__('Pending')}}</option>
                                                <option value="Completed" {{$order->payment_status == 'Completed' ? 'selected' : ''}}>{{__('Completed')}}</option>
                                        </select>
                                    </form>
                                @else
                                    @if ($order->payment_status == 'Pending' || $order->payment_status == 'pending')
                                        <p class="badge badge-danger">{{__('Pending')}}</p>
                                    @else
                                        <p class="badge badge-success">{{__('Completed')}}</p>
                                    @endif
                                @endif
                              </td>
                              <td>
                                <form id="statusForm{{$order->id}}" class="d-inline-block" action="{{route('admin.product.orders.status')}}" method="post">
                                  @csrf
                                  <input type="hidden" name="order_id" value="{{$order->id}}">
                                  <select class="form-control w-auto
                                  @if ($order->order_status == 'pending')

                                  @elseif ($order->order_status == 'received')
                                    bg-secondary
                                  @elseif ($order->order_status == 'preparing')
                                    bg-warning
                                  @elseif ($order->order_status == 'ready_to_pick_up')
                                    bg-primary
                                  @elseif ($order->order_status == 'picked_up')
                                    bg-info
                                  @elseif ($order->order_status == 'delivered')
                                    bg-success
                                  @elseif ($order->order_status == 'cancelled')
                                    bg-danger
                                  @elseif ($order->order_status == 'ready_to_serve')
                                    bg-white text-dark
                                  @elseif ($order->order_status == 'served')
                                    bg-light text-dark
                                  @endif
                                  " name="order_status" onchange="document.getElementById('statusForm{{$order->id}}').submit();">
                                    <option value="pending" {{$order->order_status == 'pending' ? 'selected' : ''}}>{{__('Pending')}}</option>
                                    <option value="received" {{$order->order_status == 'received' ? 'selected' : ''}}>{{__('Received')}}</option>
                                    <option value="preparing" {{$order->order_status == 'preparing' ? 'selected' : ''}}>{{__('Preparing')}}</option>

                                    @if ($order->serving_method != 'on_table')
                                    <option value="ready_to_pick_up" {{$order->order_status == 'ready_to_pick_up' ? 'selected' : ''}}>{{__('Ready to pick up')}}</option>
                                    <option value="picked_up" {{$order->order_status == 'picked_up' ? 'selected' : ''}}>{{__('Picked up')}}</option>
                                    @endif

                                    @if ($order->serving_method == 'home_delivery')
                                    <option value="delivered" {{$order->order_status == 'delivered' ? 'selected' : ''}}>{{__('Delivered')}}</option>
                                    @endif

                                    @if ($order->serving_method == 'on_table')
                                    <option value="ready_to_serve" {{$order->order_status == 'ready_to_serve' ? 'selected' : ''}}>{{__('Ready to Serve')}}</option>
                                    <option value="served" {{$order->order_status == 'served' ? 'selected' : ''}}>{{__('Served')}}</option>
                                    @endif

                                    <option value="cancelled" {{$order->order_status == 'cancelled' ? 'selected' : ''}}>{{__('Cancelled')}}</option>
                                  </select>
                                </form>
                              </td>
                              <td>
                                <form id="completeForm{{$order->id}}" class="d-inline-block" action="{{route('admin.product.order.completed')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{$order->id}}">
                                    <select class="form-control form-control-sm form-rounded text-light w-auto
                                        @if (empty($order->completed) || $order->completed == 'no')
                                            bg-danger
                                        @elseif ($order->completed == 'yes')
                                            bg-success
                                        @endif
                                    " name="completed" onchange="document.getElementById('completeForm{{$order->id}}').submit();">
                                            <option value="no" {{empty($order->completed) || $order->completed == 'no' ? 'selected' : ''}}>{{__('No')}}</option>
                                            <option value="yes" {{$order->completed == 'yes' ? 'selected' : ''}}>{{__('Yes')}}</option>
                                    </select>
                                  </form>
                              </td>
                              <td class="text-capitalize">
                                  {{$order->method}}
                              </td>
                              <td>
                                  {{$order->created_at}}
                              </td>

                              <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      {{__('Action')}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <a class="dropdown-item" href="{{route('admin.product.details',$order->id)}}">{{__('Details')}}</a>

                                      @if ($order->type != 'pos')
                                      <a class="dropdown-item" href="{{asset('assets/front/invoices/product/'.$order->invoice_number)}}">{{__('Invoice')}}</a>
                                      @endif

                                      <a class="dropdown-item" href="#">
                                        <form class="deleteform d-inline-block" action="{{route('admin.product.order.delete')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{$order->id}}">
                                            <button type="submit" class="deletebtn">
                                              {{__('Delete')}}
                                            </button>
                                        </form>
                                      </a>
                                      <a class="dropdown-item" id="printReceiptBtn" onclick="printReceipt('{{route('admin.printReceipt',$order->id)}}');"><i class="fas fa-print"></i> {{__('Print')}}</a>
                                    </div>
                                </div>

                              </td>
                            </tr>

                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{$orders->appends(['orders_from' => request()->input('orders_from'), 'serving_method' => request()->input('serving_method'), 'order_status' => request()->input('order_status'), 'payment_status' => request()->input('payment_status'), 'completed' => request()->input('completed'), 'order_date' => request()->input('order_date'), 'delivery_date' => request()->input('delivery_date'), 'search' => request()->input('search')])->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
        @if(!empty($order))
      <iframe id="receiptToPrint" style="display:none">

      </iframe>
      @endif
@endsection

@section('scripts')
    <script>
        var showMini = 0;
        minimize_sidebar = true;
        var popup;

        function closePrint () {
            if (popup) {
                popup.close();
            }
        }
        function togsidebar() {
            console.log('minimize');
            let w = window.innerWidth
            || document.documentElement.clientWidth
            || document.body.clientWidth;

            if (w <= 1475) {
                $(".wrapper").addClass('sidebar_minimize');
                $("button.btn.btn-toggle.toggle-sidebar").addClass('toggled');
                $("button.btn.btn-toggle.toggle-sidebar.toggled i").attr('class', 'icon-options-vertical');
                showMini = 1;
            } else if (w <= 991) {
                $(".wrapper").removeClass('sidebar_minimize');
                $("button.btn.btn-toggle.toggle-sidebar").removeClass('toggled');
                $("button.btn.btn-toggle.toggle-sidebar i").attr('class', 'icon-menu');
                showMini = 0;
            } else {
                $(".wrapper").removeClass('sidebar_minimize');
                $("button.btn.btn-toggle.toggle-sidebar").removeClass('toggled');
                $("button.btn.btn-toggle.toggle-sidebar i").attr('class', 'icon-menu');
                showMini = 0;
            }
        }

        $(document).ready(function() {
            togsidebar();

            $(".btn-toggle").on('click', function() {
                if (showMini == 1) {
                    $('.wrapper').removeClass('sidebar_minimize');
                    $(".btn-toggle").removeClass('toggled');
                    $(".btn-toggle i").attr('class', 'icon-menu');
                    showMini = 0;
                    console.log('if');
                } else {
                    console.log('else');
                    $('.wrapper').addClass('sidebar_minimize');
                    $(".btn-toggle").addClass('toggled');
                    $(".btn-toggle i").attr('class', 'icon-options-vertical');
                    showMini = 1;
                }
                console.log(showMini);
            });
        });

        $(window).resize(function() {
            togsidebar();
        })
    </script>
    @if(!empty($order))
<script>
    function printReceipt(link) {
     $.ajax({
        url: link,
        type: 'GET',
        dataType: 'html',
        success: (resp) => {
            if(resp) {
            console.log(resp);
                var iframe = document.getElementById('receiptToPrint');
                if(iframe) {
                    iframe.srcdoc=`${resp}`
                    document.body.appendChild(iframe);
                    iframe.focus();
                    iframe.contentWindow.print();
                }
                }
            }
        });
        }
</script>
<script>
var pusher = new Pusher('bd457a6ed0c247922b06', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('order-placed-channel');
    channel.bind('order-placed-event', async function (data) {
    console.log(data);
         var iframe = document.getElementById('receiptToPrint');
                if(iframe) {
                    iframe.setAttribute('data-order-id', data);
                }
        await printReceipt("{{route('admin.printReceipt','')}}/"+data.order);
    });
</script>
@endif
@endsection
