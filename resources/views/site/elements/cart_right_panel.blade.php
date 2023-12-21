@php
$currentLang = App::getLocale();
$cartAmount = 0;
$cartAmount = Helper::getCartAmount();
$checkCartDetails    = Helper::getCartItemDetails();
//dd($checkCartDetails);
if ($checkCartDetails['productExist'] > 0) {
@endphp
    <a href="#checkout_cart" id="mobile_cart_checkout" class="btn goto_cart_btn">@lang('custom.mob_home_cart_checkout_btn') CHF <span id="mobile_cart_amount"></span></a>
@php
} else {
@endphp
    <a href="#checkout_cart" id="mobile_cart_checkout" class="btn goto_cart_btn" style="display: none;">@lang('custom.mob_home_cart_checkout_btn') CHF <span id="mobile_cart_amount"></span></a>
@php
}
@endphp

<div class="order_box" id="cart_block"> 
    <h2 class="heading">@lang('custom.lab_your_order')</h2>
    <label class="deliverySwitch">
        <input type="checkbox" checked>
        <div class="switchInner">
            <span class="on delivery_option_switcher" data-deliveryoption="Delivery"><i class="siteicon icon_scooter"></i>@lang('custom.lab_delivery')</span>
            <span class="off delivery_option_switcher" data-deliveryoption="Click & Collect"><i class="siteicon icon_walking"></i>@lang('custom.lab_click_collect')</span>
        </div>
    </label>
    <input type="hidden" id="delivery_option" value="Delivery">
    
    {{-- Minimum cart error message --}}
    <div id="min_cart_div" class="alert alert-warning" style="display: none;">
        <strong>@lang('custom.text_min_order_amount1')</strong><br>
        @lang('custom.text_min_order_amount2') CHF <span id="remaining_amount"></span>
    </div>

    {{-- Success message --}}
    <div id="cart_success_message" class="alert" style="display: none;"></div>
    
    {{-- Cart details will show here --}}
    <div id="cartDetails"></div>
</div>