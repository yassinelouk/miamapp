<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('Receipt')}}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'VT323', monospace;
        }
        .receipt-title {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .my-0 {
            margin-bottom: 0px;
            margin-top: 0px;
        }
        .mb-0 {
            margin-bottom: 0px;
        }
        .mt-0 {
            margin-top: 0px;
        }
        p, h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }
        .cart-item .item {
            display: flex;
        }

        .cart-item .item .qty {
            margin-right: 29px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .order-summary {
            border-top: 1px dashed #000;
            padding-top: 15px;
        }

        .order-summary .info {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .order-summary .info.total {
            font-size: 18px;
        }
        .item-name {
            max-width: 190px;
        }
        .serving-method {
            position: absolute;
            border: 2px dashed #000000;
            font-size: 20px;
            font-weight: 500;
            padding: 2px 5px;
            transform: rotate(-54deg);
            top: 46px;
            left: 2px;
        }
    </style>
</head>
<body>

    <div>
        <div class="receipt-title">
            @if ($order->serving_method !== 'on_table')
            <div class="serving-method">{{__($order->serving_method)}}</div>
            @endif
            <h2 class="my-0">{{$bs->website_title}}</h2>
            <h4 class="my-0">{{$order->order_number}}</h4>
            <span class="my-0">({{__('Receipt')}})</span>
            @php
            $addresses = explode(PHP_EOL, $bs->contact_address);
            @endphp

            <p class="my-0" style="max-width: 200px; margin: 0 auto;">{{$addresses[0]}}</p>
            <p class="my-0">{{\Carbon\Carbon::now()}}</p>
            <p class="my-0">{{request()->getHttpHost()}}</p>
            @if($order->serving_method == 'on_table')
            <h4>{{__('Table Number')}}: {{$order->table_number}}</h4>
            @endif
        </div>

        @if (!empty($order) && !empty($order->orderitems))
        @php
        $prodTotal =0;
        @endphp
        <div id="cartTable">
        @php
        $orderitems_products_id = array();
        $orderproduct_categories_id = array();
        foreach($order->orderitems as $oitem) {
            $prodTotal = $prodTotal + $oitem->total;
            array_push($orderitems_products_id, $oitem->product_id);
        }
        $orderproducts = App\Models\Product::whereIn('id',$orderitems_products_id)->get();
        foreach($orderproducts as $oproduct) {
            array_push($orderproduct_categories_id, $oproduct->category_id);
        }
        $ordercategories = App\Models\Pcategory::whereIn('id',$orderproduct_categories_id)->distinct()->get();
        @endphp
        @foreach($ordercategories as $ckey =>$category)
        <h5>{{$category->name}}</h5>
            @foreach ($order->orderitems as $key => $item)
            @php
            $id = $item["product_id"];
            $product = App\Models\Product::findOrFail($id);
            @endphp
            @if($product->category_id == $category->id)
            <div class="cart-item">
                <div class="item">
                    <div class="qty">
                        {{$item['qty']}} X
                    </div>
                    <div class="item-name">
                        <p class="text-white">{{convertUtf8($item['title'])}}</p>
                        @php
                        if (!empty($item->variations)) {
                            $prod_variation = json_decode($item->variations);
                        }
                        if (!empty($item->addons)) {
                            $prod_addons = json_decode($item->addons);
                        }
                         @endphp
                        @if (!empty($prod_variation))
                        <p>{{__("Variation")}}: {{$prod_variation->name}} : {{$prod_variation->price}}</p>
                        @endif
                        @if (!empty($prod_addons))
                        <p> {{__('Addons')}} :
                        @foreach($prod_addons as $key => $addn)
                            {{$addn->name}} ,
                        @endforeach
                        {{$item->addons_price}}
                        </p>
                        @endif

                    </div>
                </div>
                <div class="item-total">
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$item['total']}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            @endif
            @endforeach
            @endforeach
        </div>

        <div class="order-summary">
            <div class="info">
                <div>{{__('Cart Subtotal')}}:</div>
                <div>
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$prodTotal}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            <div class="info">
                <div>{{__('Tax')}}:</div>
                <div>
                    +
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$order->tax}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            @if($order->serving_method == 'home_delivery')
            <div class="info">
                <div>{{__('Shipping Charges')}}:</div>
                <div>
                    +
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$order->shipping_charge}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            @endif
            <div class="info">
                <div>{{__('Coupon')}}:</div>
                <div>
                    -
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$order->coupon}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            <div class="info">
                <div>{{__('Fidelity points')}}:</div>
                <div>
                    -
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$order->fidelity_discount}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
            <div class="info total">
                <div>Total:</div>
                <div>
                    {{$be->base_currency_text_position == 'left' ? $be->base_currency_text : ''}}
                    {{$prodTotal +$order->shipping_charge - $order->coupon - $order->fidelity_discount}}
                    {{$be->base_currency_text_position == 'right' ? $be->base_currency_text : ''}}
                </div>
            </div>
        </div>

        @endif
    </div>

</body>
</html>
