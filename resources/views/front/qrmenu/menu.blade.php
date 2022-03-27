@extends('front.qrmenu.layout')

@section('page-heading')
    {{__('Menu')}}
@endsection

@section('content')

@if($table == null)
<div class="jumbotron">
    <h1 class="display-4">Oups!</h1>
    <div class="alert alert-warning" role="alert">
        Cette table n'existe plus
    </div>
</div>
@else


    <section class="food-menu-area food-menu-2-area food-menu-3-area">
        <div class="container">
            <div class="categories-tab">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tabs-btn pb-20">
                            <ul class="nav nav-pills d-flex justify-content-center" id="pills-tab" role="tablist">
                                @foreach ($categories as $keys => $category)
                                <li class="nav-item">
                                    <a class="nav-link {{$keys == 0 ? 'active' : ''}}" id="{{$category->slug}}-tab" data-toggle="pill" href="#{{$category->slug}}" role="tab" aria-controls="{{$category->slug}}" aria-selected="true">
                                        <p>{{convertUtf8($category->name)}}</p>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content" id="pills-tabContent">
                        @foreach ($categories as $key => $category)
                        <div class="tab-pane fade {{$key == 0 ? 'show active' : ''}}"  id="{{$category->slug}}" role="tabpanel" aria-labelledby="{{$category->slug}}-tab">
                            <div class="row justify-content-center">
                                @if($category->products()->where('status', 1)->count() > 0)
                                @foreach ($category->products()->where('status', 1)->get() as $product)
                                <div class="col-lg-6">
                                    <div class="food-menu-items">
                                        <div class="single-menu-item mt-30">
                                        <div class="menu-thumb-and-text">
                                        @if (!empty($product->feature_image) && file_exists('assets/front/img/product/featured/'.$product->feature_image))
                                            <div class="menu-thumb">
                                                <img class="lazy" data-src="{{asset('assets/front/img/product/featured/'.$product->feature_image)}}" alt="menu">
                                     
                                            </div>
                                        @endif
                                            <div class="menu-content ml-30">
                                                <a class="title" href="#">{{convertUtf8($product->title)}}</a>
                                                <p>{{convertUtf8($product->summary)}} </p>
                                            </div>
                                        </div>
                                            <div class="menu-price-btn">

                                                <span>
                                                    {{$be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : ''}}{{convertUtf8($product->current_price)}}{{$be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : ''}}
                                                </span>
                                                @if($product->previous_price)
                                                <del>  {{$be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : ''}}{{convertUtf8($product->previous_price)}}{{$be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : ''}}</del>
                                                @endif
                                               <a class="cart-link" data-product="{{$product}}" data-href="{{route('add.cart',$product->id)}}"><i class="fas fa-plus"></i></a>

                                            </div>
                                            @if ($product->is_special == 1)
                                                <div class="flag flag-2"><i class="fas fa-certificate"></i></div>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="col-lg-12 bg-light py-5 mt-4">
                                    <h4 class="text-center">{{__('Product Not Found')}}</h4>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach



                    </div>
                </div>
            </div>
        </div>
    </section>
@endif


{{-- Variation Modal Starts --}}
@includeIf('front.qrmenu.partials.qr-variation-modal')
{{-- Variation Modal Ends --}}
@endsection
