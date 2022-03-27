<div class="cart-total" id="orderTotal">
    <div class="shop-title-box">
        <h3>{{ __('Order Total') }}</h3>
    </div>

    <div id="cartTotal">
        <ul class="cart-total-table">
        @php
        $current_order = Session::get('order');
        @endphp
        @if(!empty($current_order) && $current_order->total !== 0)
            <li>
                <span class="col-title">{{ __('Order Total') }}</span>
                <span>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span
                    data="{{ $current_order->total }}" class="subtotal">{{ $current_order->total }}</span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
            @endif
            <li @php if($discount == 0 && tax() == 0 && $fidelity_discount == 0) echo 'style="display:none;"' @endphp>
                <span class="col-title">{{ __('Cart Total') }}</span>
                <span>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span
                    data="{{ cartTotal() }}" class="subtotal">{{ cartTotal() }}</span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>

            <li @php if($discount == 0) echo 'style="display:none;"' @endphp>
                <span class="col-title">{{ __('Discount') }}</span>
                <span>
                    <i class="fas fa-minus"></i>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span data="{{ $discount }}">{{ $discount }}</span>
                    {{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>

            </li>
            <li @php if($fidelity_discount == 0 || $be->is_fidelity == 0) echo 'style="display:none;"' @endphp>
                @if($be->base_fidelity_rate > 0 && $be->is_fidelity == 1)<span class="col-title">{{ __('Fidelity points') }} <strong>- {{round(($fidelity_discount/$be->base_fidelity_rate),0, PHP_ROUND_HALF_DOWN)}}</strong></span> @endif
                <span>
                    <i class="fas fa-minus"></i>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span data="{{ $fidelity_discount }}">{{ $fidelity_discount }}</span>
                    {{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
            <li @php if($discount == 0 && $fidelity_discount == 0) echo 'style="display:none;"' @endphp>
                <span class="col-title">{{ __('Cart Subtotal') }}</span>
                <span>
                {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span
                    data="{{ cartTotal() - $discount - $fidelity_discount }}" class="subtotal"
                    id="subtotal">{{ cartTotal() - $discount - $fidelity_discount }}</span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
            <li @php if(tax() == 0) echo 'style="display:none;"' @endphp>
                <span class="col-title">{{ __('Tax') }}</span>
                <span>
                    <i class="fas fa-plus"></i>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span
                    data-tax="{{ tax() }}" id="tax">{{ tax() }}</span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
            <li @php echo 'style="display:none;"' @endphp>
                <span class="col-title">{{ __('Shipping Charge') }}</span>
                <span>
                <i class="fas fa-plus"></i>
                {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span
                    data="0"
                    class="shipping">0</span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
            <li>
                <span class="col-title">{{ __('Total') }}</span>
                <span>
                    {{ $be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '' }}<span data="" class="grandTotal"></span>{{ $be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '' }}
                </span>
            </li>
        </ul>
    </div>
    <div class="coupon">
        <h4 class="mb-3">{{__('Coupon')}}</h4>
        <div class="form-group d-flex">
            <input type="text" class="form-control" name="coupon" value="">
            <button class="btn btn-primary base-btn" type="button" onclick="applyCoupon();">{{__('Apply')}}</button>
        </div>
        
        @if(!Auth::check())
        <a href="{{ route('front.qrmenu.login') }}" class="btn btn-primary base-btn" ><i class="fas fa-tag"></i>   {{__('Use my fidelity points')}}</a>
        
        @endif
        @if($be->is_fidelity && Auth::check())
        <h4 class="mb-3">{{__('Fidelity points')}}</h4>
        <div class="form-group d-flex">
        @if(!Session::has('fidelity_discount'))
            <button id="fidelity-btn" class="btn btn-primary base-btn" type="button" onclick="useFidelityPoints();"><i class="fas fa-tag"></i>   {{__('Use my fidelity points')}}</button>
        @else
            <button id="fidelity-btn" class="btn btn-primary btn-danger" type="button" onclick="useFidelityPoints();"><i class="fas fa-tag"></i>   {{__('Cancel my fidelity discount')}}</button>
        @endif
        </div>
        @endif
    </div>

    <div class="payment-options">
        <span style="display:none;">@includeIf('front.product.payment-gateways')</span>
        @error('gateway')
        <p class="text-danger mb-0">{{ convertUtf8($message) }}</p>
        @enderror



    </div>
</div>


