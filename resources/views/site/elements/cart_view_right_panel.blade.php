@php
$langCode       = strtoupper(\App::getLocale());
$cartDetails    = Helper::getCartItemDetails();
$siteSettings   = Helper::getSiteSettings();
$deliveryCharge = 0;
// if (!Auth::user()) {
// $deliveryCharge += Cookie::get('delivery_charge');
// }

if (Session::get('deliveryOption') == 'Delivery') {
    if (!Auth::user()) {
        $deliveryCharge += Cookie::get('delivery_charge');
    } else {
        
    }
}
@endphp
<div class="order_box">
    <h2 class="heading">@lang('custom.lab_your_order')</h2>
    
    {{-- Cart details will show here --}}
    <div id="cartDetails">
        @if (count($cartDetails['itemDetails']) > 0)
            {{-- Cart details --}}
            <div class="table-responsive">
                <table>
                    <tbody>
                    @foreach ($cartDetails['itemDetails'] as $keyItem => $valIem)
                        <tr>
                            <td><strong>{{$valIem['quantity']}}x</strong></td>
                            <td>
                                <strong>{{$valIem['local_details'][$langCode]['local_title']}}</strong>
                                {{-- Menu --}}
                                @if ($valIem['is_menu'] == 'Y' && count($valIem['menu_title_value_local_details']) > 0)
                                    <div class="mt5">
                                        @php
                                        $menu = 1;
                                        foreach ($valIem['menu_title_value_local_details'][$langCode] as $keyMenu => $valMenu) {
                                            // echo $valMenu['menu_local_title'].': '.$valMenu['menu_local_value'];
                                            if (strpos($valMenu['menu_local_value'], '#') !== false) {
                                                $explodedValue = explode('#', $valMenu['menu_local_value']);
                                                $d = 1;
                                                foreach ($explodedValue as $keyRe => $valueRe) {
                                                    echo '+ '.$valueRe;
                                                    if ($d < count($explodedValue)) {
                                                        echo '<br>';
                                                    }
                                                    $d++;
                                                }
                                            } else {
                                                echo '+ '.$valMenu['menu_local_value'];
                                            }
                                            if ($menu < count($valIem['menu_title_value_local_details'][$langCode])) {
                                                echo '<br>';
                                            }
                                            $menu++;
                                        }
                                        @endphp
                                    </div>
                                @endif

                                {{-- Ingredients --}}
                                @if ($valIem['has_ingredients'] == 'Y' && count($valIem['ingredient_local_details']) > 0)
                                    <div class="mt5">
                                        @php
                                        $ingred = 1;
                                        foreach ($valIem['ingredient_local_details'] as $ingredient) {
                                            echo $ingredient[$langCode]['local_title'];
                                            if ($ingred < count($valIem['ingredient_local_details'])) {
                                                echo ', ';
                                            }
                                            $ingred++;
                                        }
                                        @endphp
                                    </div>
                                @endif

                                {{-- Attributes --}}
                                @if ($valIem['has_attribute'] == 'Y')
                                    <div class="mt5">
                                        @php
                                        echo $valIem['attribute_local_details'][$langCode]['local_title'];
                                        @endphp
                                    </div>
                                @endif
                            </td>

                            <td class="text-right"><strong>CHF {{$valIem['total_price']}}</strong></td>
                        </tr>
                    @endforeach

                        <tr class="cart_total">
                            <td colspan="2">@lang('custom.label_subtotal')</td>
                            <td class="text-right">CHF {{Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'])}}</td>
                        </tr>

                        @if (Session::get('deliveryOption') == 'Delivery')
                        <tr class="cart_total" id="only_delivery_section" @if ($deliveryCharge != 0)style="display: table-row;" @else style="display: none;" @endif>
                            <td colspan="2">@lang('custom.lab_delivery_charge')</td>
                            <td class="text-right"><span id="deliveryCharge">CHF {{Helper::formatToTwoDecimalPlaces($deliveryCharge)}}</span></td>
                        </tr>
                        {{ Form::hidden('totalPrice', $cartDetails['totalCartPrice'], array('id' => 'totalPrice', 'class' => 'form-control')) }}
                        @endif
                        <tr class="cart_total" id="cartTotal" style="display: none;">
                            <td colspan="2"><strong>@lang('custom.total')</strong></td>
                            <td class="text-right"><strong id="totalPriceWithDelivery">CHF {{Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'] + $deliveryCharge)}}</strong></td>
                        </tr>

                        {{-- Card Payment --}}
                        <tr class="cart_total" id="cardPaymentAmountSection" style="display: none;">
                            <td colspan="2">@lang('custom.label_new_card_payment')</td>
                            <td class="text-right"><span id="cardPaymentAmount">CHF 0.00</span></td>
                        </tr>
                        {{-- Card Payment --}}

                        <tr class="cart_total green" id="discountAmountSection" style="display: none;">
                            <td colspan="2">@lang('custom.discount')</td>
                            <td class="text-right"><span id="discount">CHF 0.00</span></td>
                        </tr>

                        <tr class="cart_total" id="netPayableAmount">
                            <td colspan="2">
                                <strong>@lang('custom.net_payable')</strong>
                            @if ($siteSettings['mwst_number'] != null)
                                <br>
                                <small style="font-size: 10px;">(@lang('custom_admin.label_total_vat'))</small>
                            @endif
                            </td>
                            @php $netPay = Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'] + $deliveryCharge); @endphp
                            <td class="text-right">
                                <strong id="netPayableWithDelivery">
                                    CHF {{ Helper::priceRoundOff($netPay) }}
                                </strong>
                            </td>
                        </tr>

                        {{-- Apply coupon --}}
                        <tr class="cart_total" id="">
                            <td colspan="3">
                                <strong class="couponTxt">@lang('custom.label_apply_coupon')</strong>
                                
                                <div class="couponBox">
                                    <div class="couponInput">
                                        {{ Form::text('coupon_code', null, array(
                                                                    'id' => 'coupon_code',
                                                                    'placeholder' => trans('custom.label_coupon_placeholder'),
                                                                    'class' => 'form-control couponCode',
                                                                    )) }}

                                        {{ Form::hidden('delvry_chrg', Helper::formatToTwoDecimalPlaces($deliveryCharge), array('id' => 'delvry_chrg')) }}
                                        {{ Form::hidden('net_pay', Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'] + $deliveryCharge), array('id' => 'net_pay')) }}
                                        {{ Form::hidden('disc_amount', Helper::formatToTwoDecimalPlaces(0), array('id' => 'disc_amount')) }}
                                        {{ Form::hidden('card_amount', Helper::formatToTwoDecimalPlaces(0), array('id' => 'card_amount')) }}

                                        {{ Form::hidden('net_payable_amount', Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'] + $deliveryCharge), array('id' => 'net_payable_amount')) }}

                                        <a href="#" id="remove_coupon" onclick="return sweetAlertRemoveCoupon(this, '{{trans("custom.are_you_sure_to_delete_coupon")}}', 'warning',  true)" href="javascript:void(0)" data-href="{{route('site.'.\App::getLocale().'.remove-coupon')}}" class="coupon_remove" style="display: none;">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <a href="#" class="btn" id="apply-coupon">@lang('custom.label_apply_now')</a>
                                    
                                    <div id="coupon_apply_remove_message"></div>
                                </div>
                            </td>
                        </tr>
                        {{-- Apply coupon --}}

                    </tbody>
                </table>
            </div>
        @else
            <div class="noOrder">
                <i class="siteicon icon_busket"></i>
                <h3 class="subheading">@lang('custom.message_empty_order')</h3>
                <div>@lang('custom.suggession_empty_cart')</div>
            </div>
        @endif
    </div>
</div>