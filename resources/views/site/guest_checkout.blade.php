@extends('site.layouts.app', [])
    @section('content')
    @php
    $dlvryPinCode = null;
    if (Cookie::get('pincode')) {
        $dlvryPinCode = Cookie::get('pincode');
    } else {
        if ($deliveryAddresses) {
            $dlvryPinCode = $deliveryAddresses->post_code;
        }
    }
    $dlivryChrge = 0;
    $dlivryChrge += Cookie::get('delivery_charge');
    $gettingShopStatus = Helper::gettingShopStatusFlag();
    $logoImage=Helper::getSettingImage('logo'); 
    $siteSetting=Helper::getSiteSettings();
    @endphp
        
	<main class="mainContainer">
        <section class="section">
            <div class="container">
                <form method="POST" id="guestCheckoutForm" name="guestCheckoutForm" autocomplete="off">
                    <article class="tt_container no_left_sidebar1 stickyContent">
                        <div class="form_wrap form_box">
                            @include('site.elements.notification')

                            <h1 class="heading heading_large">@lang('custom.message_order_is_being_delivered'):
                                <div class="logo"><img src="@if($logoImage){{$logoImage}}@else{{asset('images/site/logo.png')}}@endif" alt="{{$siteSetting->website_title}}"> {{$siteSetting->website_title}}</div>
                            </h1>

                            <ul class="row">
                                <li class="col-md-6">
                                    <div class="form-group">
                                        <label class="labelWrap iconLabelWrap dateWrap">
                                            <span>@lang('custom.labe_delivery_date')</span>
                                            {{ Form::text('delivery_date', date('d/m/Y'), array(
                                                                'id' => 'delivery_date',
                                                                'placeholder' => 'dd/mm/yyyy',
                                                                'class' => 'form-control datepicker getDeliverySlots',
                                                                'required' => 'required'
                                                                    )) }}
                                            <i class="icon-calendar"></i>
                                        </label>
                                        <div class="help_block"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="labelWrap iconLabelWrap">
                                            <span>@lang('custom.labe_delivery_time')</span>
                                            <select name="delivery_time" id="delivery_time">
                                             <option value="">@lang('custom.option_select')</option> 
                                              @php
											      $delsloats=Helper::generateDeliverySlotNew('options');
											  @endphp 
                                            </select>
                                            <i class="icon-clock"></i>
                                        </label>
                                        <div class="help_block"></div>
                                    </div>
                                    <input type="hidden" name="is_as_soon_as_possible" id="is_as_soon_as_possible" value="">
                                    <div class="form-group">
                                        <label class="labelWrap iconLabelWrap">
                                            <span>@lang('custom.labe_first_last_name') *</span>
                                            {{ Form::text('full_name', null, array(
                                                                'id' => 'full_name',
                                                                'placeholder' => trans('custom.labe_first_last_name').' *',
                                                                'class' => 'form-control text-only',
                                                                'required' => 'required'
                                                                    )) }}
                                            <i class="icon-user"></i>
                                        </label>
                                        <div class="help_block"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="labelWrap iconLabelWrap ">{{--contactNo--}}
                                            <span>@lang('custom.label_contact_number') *</span>
                                            {{--<em>+49</em>--}}
                                            {{ Form::text('phone_no', null, array(
                                                                'id' => 'phone_no',
                                                                'placeholder' => trans('custom.placeholder_contact_number').' *',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                    )) }}
                                            <i class="icon-phone"></i>
                                        </label>
                                        <div class="help_block"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="labelWrap iconLabelWrap">
                                            <span>@lang('custom.label_email') *</span>
                                            {{ Form::text('email', null, array(
                                                                'id' => 'email',
                                                                'placeholder' => trans('custom.label_email').' *',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                    )) }}
                                            <i class="icon-envelope"></i>
                                        </label>
                                        <div class="help_block"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="labelWrap1 payment_option">
                                          @php   $paymentSettings = Helper::getPaymentSettings(); @endphp 
                                           @if($paymentSettings)
                                            <span>@lang('custom.label_payment_mode')</span>
                                            @if($paymentSettings->cash_method=='Y')
                                                <label class="input_radio">
                                                    <input type="radio" name="payment_method" value="1" class="paymentMethod" checked>
                                                    <span>
                                                        <figure class="tt_payment payment_cash"></figure>
                                                        @lang('custom.label_cash_payment')
                                                    </span>
                                                </label>
                                            @endif
                                            @if($paymentSettings->stripe_method=='Y' && $paymentSettings->stripe_active)
                                            <label class="input_radio">
                                                <input type="radio" name="payment_method" value="2" class="paymentMethod">
                                                <span>
                                                    <figure class="tt_payment payment_card"></figure>
                                                    @lang('custom.label_card_payment')
                                                </span>
                                            </label>
                                            @endif                                           
                                            @if($paymentSettings->payrexx_method=='Y' && $paymentSettings->payrexx_active)
                                                <label class="input_radio">
                                                    <input type="radio" name="payment_method" value="4" class="paymentMethod">
                                                    <span>
                                                        <figure class="tt_payment payment_carddoor"></figure>
                                                        @lang('custom.label_card_payment')
                                                    </span>
                                                </label>
                                            @endif
                                            @if($paymentSettings->door_method=='Y')
                                                <label class="input_radio">
                                                    <input type="radio" name="payment_method" value="3" class="paymentMethod">
                                                    <span>
                                                        <figure class="tt_payment payment_carddoor"></figure>
                                                        @lang('custom.label_card_on_door')
                                                    </span>
                                                </label>
                                            @endif
                                          @else
                                          @endif
                                        </div>
                                        <div class="help_block"></div>
                                    </div>
                                </li>
                               
                                <li class="col-md-6 @if($deliveryOptioncheck=='Click & Collect') clickandsend_option hide @endif">
                                    <div class="form-group formHeading-group">
                                        <div class="formHeader">
                                            <div class="tt_fleft"><i class="siteicon icon_home"></i> @lang('custom.label_guest_address')</div>
                                        </div>
                                        <div class="formBody">
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_company')</span>
                                                    {{ Form::text('company', isset($deliveryAddresses->company) ? $deliveryAddresses->company : null, array(
                                                            'id' => 'company',
                                                            'placeholder' => trans('custom.label_company'),
                                                            'class' => 'form-control'
                                                            )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_street') *</span>
                                                    {{ Form::text('street', isset($deliveryAddresses->street) ? $deliveryAddresses->street : null, array(
                                                            'id' => 'street',
                                                            'placeholder' => trans('custom.label_street').' *',
                                                            'class' => 'form-control',
                                                            'required' => 'required'
                                                            )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_floor')</span>
                                                    {{ Form::text('floor', isset($deliveryAddresses->floor) ? $deliveryAddresses->floor : null, array(
                                                        'id' => 'floor',
                                                        'placeholder' => trans('custom.label_floor'),
                                                        'class' => 'form-control',
                                                        )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_door_code')</span>
                                                    {{ Form::text('door_code', isset($deliveryAddresses->door_code) ? $deliveryAddresses->door_code : null, array(
                                                        'id' => 'door_code',
                                                        'placeholder' => trans('custom.label_door_code').' *',
                                                        'class' => 'form-control',
                                                        )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_postcode') *</span>
                                                    {{ Form::text('post_code', $dlvryPinCode, array(
                                                        'id' => 'post_code',
                                                        'placeholder' => trans('custom.label_postcode').' *',
                                                        'class' => 'form-control',
                                                        'required' => 'required',
                                                        'readonly' => true
                                                        )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="labelWrap">
                                                    <span>@lang('custom.label_city') *</span>
                                                    {{ Form::text('city', isset($deliveryAddresses->city) ? $deliveryAddresses->city : null, array(
                                                        'id' => 'city',
                                                        'placeholder' => trans('custom.label_city').' *',
                                                        'class' => 'form-control',
                                                        'required' => 'required'
                                                        )) }}
                                                </label>
                                                <div class="help_block"></div>
                                            </div>
                                        </div>
                                        <div class="help_block"></div>
                                    </div>
                                    <div class="alert alert-info withIcon">
                                        <i class="fa fa-exclamation"></i>
                                        <a href="javascript:void(0);" data-toggle="tt_modal" data-target="#allergy_modal">@lang('custom.text_allergen')</a>
                                    </div>
                                </li>
                               
                            </ul>
                        </div>
                    </article>
                    <aside class="tt_sidebar sidebar_right stickySidebar">
                        @include('site.elements.cart_view_right_panel')

                        <div class="order_box">
                            <div class="form-group">
                                <label class="labelWrap1">
                                    <span class="hideLabel">&nbsp;</span>
                                    <textarea placeholder="@lang('custom.message_checkout_extra_note')" name="checkout_message" id
                                ="checkout_message" class="showPlaceholder"></textarea>
                                </label>
                                <div class="help_block"></div>
                            </div>
                        </div>

                        {{ Form::hidden('delivery_charge', $dlivryChrge, array('class' => 'form-control')) }}
                        @php
                            $deliveryCharge = 0;
                            if (Session::get('deliveryOption') == 'Delivery') {
                                if (!Auth::user()) {
                                    $deliveryCharge += Cookie::get('delivery_charge');
                                } else {
                                    
                                }
                            }
                         $netPay = Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'] + $deliveryCharge); 
                         @endphp
                        <div class="order_box">
                            <button type="submit" class="btn btn-width goToPayment">@lang('custom.checkout_page_order_btn') <span class="final_amount_show">{{$netPay}}</span> CHF</button>
                            <div class="mt25"><a href="{{route('site.'.\App::getLocale().'.home')}}" class="returnToRestaurant"><strong>@lang('custom.return_to_restaurant')</strong></a></div>
                        </div>
                    </aside>
                </form>
            </div>
        </section>
    </main>

    @include('site.elements.popups')

    @push('stripe-payment')
        <div id="guestPaymentForm">
            @include('site.elements.guest_payment_form')
        </div>
    @endpush

	@endsection