<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$bs->website_title}}</title>

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="{{asset('assets/front/img/'.$bs->favicon)}}" type="image/png">
    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="{{asset('assets/front/css/bootstrap.min.css')}}">
    <link type="text/css" rel="stylesheet" href="{{asset('assets/front/css/qr-plugins.css')}}">
    <link rel="stylesheet" href="{{asset('assets/front/css/qr-menu.css')}}">
    @if ($currentLang->rtl == 1)
    <link rel="stylesheet" href="{{asset('assets/front/css/qr-rtl.css')}}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/front/css/qr-styles.php?color='.str_replace('#','',$bs->base_color)) }}">
    <!--====== jquery js ======-->
    <script src="{{asset('assets/front/js/vendor/jquery.3.2.1.min.js')}}"></script>
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" data-auto-replace-svg="nest"></script>
        <style>
    .card-round {
    border-radius: 30px;
    }
    a,button{
            border-radius: 15px !important;
            margin-left:5px !important;
    }
    a i{
            border-radius: 10px !important;
    }
    .qr-menu .nav-pills .nav-link {
    border: 1px solid;
}
.food-menu-area.food-menu-3-area .food-menu-items .single-menu-item .menu-price-btn a , .food-menu-area.food-menu-3-area .food-menu-items .single-menu-item .menu-price-btn span{
    position:relative !important;
}
.food-menu-area.food-menu-3-area .food-menu-items .single-menu-item .menu-price-btn a , .food-menu-area.food-menu-3-area .food-menu-items .single-menu-item .menu-price-btn{
    text-align:right;
}
.food-menu-area .food-menu-items .single-menu-item .menu-price-btn a{
    transform:initial;
}
.food-menu-area .food-menu-items .single-menu-item .menu-price-btn .cart-link i {
    margin-left: auto;
}
.modal-quantity span {
    border: 2px solid #3d9715;
    border-radius: 15px;
}
.cart-icon span.cart-count {
    background-color: #f3a530;
    border-radius: 50%;
}
.cart-icon {
    border-radius: 11px;
}
.cart-sidebar .qty span {
    border: 1px solid #3d9715;
    border-radius: 8px;
    padding: 2px 5px;
}
.cart-sidebar .cart-body {
    bottom: 140px !important;
}
.cart-sidebar .cart-total {
    background: white !important;
}
  </style>
</head>
<body class="qr-menu">
    <div class="header">
        <div class="container">


            <div class="row no-gutters align-items-center">

                <div class="col-3">

                    <div class="logo-wrapper">
                        <a href="{{route('front.qrmenu')}}"><img src="{{asset('assets/front/img/'.$bs->logo)}}" alt="Logo"></a>
                    </div>
                </div>

                <div class="col-9 d-flex justify-content-end">
                    <form id="langForm" action="" class='mr-2'>
                        <select class="form-control form-control-md" onchange="document.getElementById('langForm').setAttribute('action', '{{url('changelanguage')}}' + '/' + this.value + '/qr'); document.getElementById('langForm').submit()">
                            @foreach($langs as $lang)
                            <option value="{{$lang->code}}" {{$currentLang->code == $lang->code ? 'selected' : ''}}>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    </form>
                    @if (Auth::check() || $bs->qr_call_waiter == 1)
                        <div class="dropdown">
                            <button class="btn base-btn text-white dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <!--<i class="fas fa-bars"></i>-->
                                <i class="fas fa-concierge-bell"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                @if($bs->qr_call_waiter == 1)
                                <a class="dropdown-item" data-toggle="modal" data-target="#callWaiterModal">{{__('Call Waiter')}}</a>
                                @endif
                                @if(Session::has('order'))
                                    <form  enctype="multipart/form-data" method="POST" name="askforbill" id="askforbill" action="{{route('product.payment.qrsaveorder', 'close')}}">
                                    @csrf
                                        {{-- <button class="dropdown-item" id="askForBillBtn">{{__('Ask for the bill')}}</button> --}}
                                    </form>
                                @endif
                                @auth
                                <a class="dropdown-item" href="{{route('front.qrmenu.logout')}}">{{__('Logout')}}</a>
                                <div class="dropdown-item fidelity-dropdown">{{__('Fidelity points')}}: {{Auth::user()->fidelity_points}}</div>
                                @endauth
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!--<div class="qr-breadcrumb lazy" data-bg="{{asset('assets/front/img/'.$bs->breadcrumb)}}">
        <div class="container">
            <div class="qr-breadcrumb-details">
                <h2>{{$bs->website_title}}</h2>
                <small>{{__('Working Hours')}}: {{$bs->office_time}}</small>
            </div>
            <h4 class="qr-page-heading">
                @yield('page-heading')
            </h4>
        </div>
    </div>-->


    @yield('content')

    {{-- Loader --}}
    <div class="request-loader">
        <img src="{{asset('assets/admin/img/loader.gif')}}" alt="">
    </div>
    {{-- Loader --}}

    {{-- START: Cart Icon --}}
    <div class="cart-icon">
    <i class="fas fa-shopping-cart"></i>
        <div id="cartQuantity">
            <span class="cart-count">{{$itemsCount}}</span>
        </div>
    </div>
    {{-- END: Cart Icon --}}


    {{-- START: Cart Sidebar --}}
    @includeIf('front.qrmenu.partials.qr-cart-sidebar')
    {{-- END: Cart Sidebar --}}


    {{-- START: Call Waiter Modal --}}
    <div class="modal fade" id="callWaiterModal" tabindex="-1" role="dialog" aria-labelledby="callWaiterModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Call Waiter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $tables = \App\Table::where('status', 1)->get();
                        if(!empty($_GET)) {
                            $table_id = Session::put('table_id', $_GET['table']);
                        }
                    @endphp
                   <form id="callWaiterForm" action="{{route('front.callwaiter')}}" method="GET">
                        <select class="form-control" name="table" disabled required>
                            <option value="{{ session()->get('table_id') }}">{{__('Table')}} - {{ session()->get('table_id') }} </option>
                        </select>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="callWaiterForm" class="btn base-btn text-white">{{__('Call Waiter')}}</button>
                </div>
            </div>
        </div>
    </div>
    {{-- END: Call Waiter Modal --}}

    {{-- global variables --}}
    <script>
        var mainurl = "{{url('/')}}";
        var position = "{{$be->base_currency_symbol_position}}";
        var symbol = "{{$be->base_currency_symbol}}";
        var textPosition = "{{$be->base_currency_text_position}}";
        var currText = "{{$be->base_currency_text}}";
    </script>
    <!--====== Bootstrap js ======-->
    <script src="{{asset('assets/front/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/front/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/front/js/qr-plugins.js')}}"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $( "input.datepicker" ).datepicker();
            $('input.timepicker').timepicker();
        });

        $(document).on('click', '.qty-add', function() {
            $(".cart-sidebar-loader-container").addClass('show');

            let $this = $(this);
            let key = $(this).data('key');
            let $input = $this.prev('input');
            $input.val(parseInt($input.val()) + 1);
            let qty = $input.val();

            changeQty(key, qty);
        });

        $(document).on('click', '.qty-sub', function() {
            $(".cart-sidebar-loader-container").addClass('show');

            let $this = $(this);
            let key = $(this).data('key');
            let $input = $this.next('input');
            if ($input.val() <= 1) {
                toastr["error"]("Quantity must be minimum 1");
                $(".cart-sidebar-loader-container").removeClass('show');
                return;
            }
            $input.val(parseInt($input.val()) - 1);
            let qty = $input.val();

            changeQty(key, qty);
        });

        function changeQty(key, qty) {
            let fd = new FormData();
            fd.append('qty', qty);
            fd.append('key', key);
            $.ajax({
                url: "{{route('front.qr.qtyChange')}}",
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {
                    toastr['success']('Quantity updated');
                    $("#cartQuantity").load(location.href + " #cartQuantity");
                    $("#refreshDiv").load(location.href + " #refreshDiv", function() {
                        setTimeout(function() {
                            $(".cart-sidebar-loader-container").removeClass('show');
                        }, 100);
                    });
                }
            })
        }


        $(document).on('click', '.cart-item .close', function() {
            $(".cart-sidebar-loader-container").addClass('show');
            let $this = $(this);
            let key = $this.data('key');
            let fd = new FormData();
            fd.append('key', key);

            $.ajax({
                url: "{{route('front.qr.remove')}}",
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {
                    // console.log(data);
                    toastr['success']('Item removed');
                    $("#cartQuantity").load(location.href + " #cartQuantity");
                    $("#refreshDiv").load(location.href + " #refreshDiv", function() {
                        setTimeout(function() {
                            $(".cart-sidebar-loader-container").removeClass('show');
                        }, 100);
                    })
                }
            })
        })
    </script>
    <script>
    if (document.querySelector('.login-title')) {
        document.querySelector('.login-title').addEventListener('click', function() {
            if(document.querySelector('#loginForm').style.display=="none") {document.querySelector('#loginForm').style.display="block"}
            else{document.querySelector('#loginForm').style.display="none"}
        })
    }
    </script>
    <script src="{{asset('assets/front/js/qr-cart.js')}}"></script>

    @if (session()->has('success'))
    <script>
        "use strict";
        toastr["success"]("{{__(session('success'))}}");
    </script>
    @endif

    @if (session()->has('warning'))
    <script>
        "use strict";
        toastr["warning"]("{{__(session('warning'))}}");
    </script>
    @endif

    @if (session()->has('error'))
    <script>
        "use strict";
        toastr["error"]("{{__(session('error'))}}");
    </script>
    @endif
    @yield('script')
</body>
</html>
