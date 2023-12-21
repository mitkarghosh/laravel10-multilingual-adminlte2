<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Vadodara:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap">
	<title>{{ $title }}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="{{ $keyword }}" />
	<meta name="description" content="{{ $description }}" />
	<meta name="theme-color" content="#e32727">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<link rel="shortcut icon" type="image/x-icon" sizes="16x16" href="{{ asset('images/site/favicon.ico') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/site/plugins.min.css') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/site/custom.css') }}?123456"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/site/responsive.css') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/site/development.css') }}"/>
 
    <!--[if IE 7]> <html class="ie7"> <![endif]-->
    <!--[if IE 8]> <html class="ie8"> <![endif]-->
    <!--[if IE 9]> <html class="ie9"> <![endif]-->
</head>
@php
$currentLang = App::getLocale();
$cartAmount = 0;
$checkCartDetails    = Helper::getCartItemDetails();
if(Route::current()->getName() == 'site.'.$currentLang.'.home' || Route::current()->getName() == 'site.'.$currentLang.'.reviews' || Route::current()->getName() == 'site.'.$currentLang.'.info' || Route::current()->getName() == 'site.'.$currentLang.'.checkout' || Route::current()->getName() == 'site.'.$currentLang.'.guest-checkout') {
	$cartAmount = Helper::getCartAmount();
}
$payment_setting    = Helper::getPaymentSettings();
@endphp

@php $payrexx_method=!empty($payment_setting)?$payment_setting->payrexx_method:'' @endphp
@if(Route::current()->getName() == 'site.'.$currentLang.'.home')
<body class="@lang('custom.body_class')">
@elseif(Route::current()->getName() == 'site.'.$currentLang.'.users.register')
<body class="@lang('custom.body_class') section_register" style="background-image: url({{asset('images/site/bg_register.jpg')}});">
@elseif(Route::current()->getName() == 'site.'.$currentLang.'.users.login' || Route::current()->getName() == 'site.'.$currentLang.'.users.forgot-password' || Route::current()->getName() == 'site.'.$currentLang.'.users.reset-password')
<body class="@lang('custom.body_class') section_login" style="background-image: url({{asset('images/site/bg_login.jpg')}});">
@else
<body class="@lang('custom.body_class')">
@endif
	<div class="bodyOverlay"></div>
	<div class="responsive_nav"></div>
	<a class="scrollup" href="javascript:void(0);" aria-label="Scroll to top"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>

	<div class="mainBody @if ($checkCartDetails['productExist'] > 0) withCartValue @endif">

		@include('site.elements.header')

		<main class="mainContainer header_tr">
			@yield('content')
		</main>

	@if(Route::current()->getName() == 'site.'.$currentLang.'.home' || Route::current()->getName() == 'site.'.$currentLang.'.info' || Route::current()->getName() == 'site.'.$currentLang.'.help' || Route::current()->getName() == 'site.'.$currentLang.'.help-details' || Route::current()->getName() == 'site.'.$currentLang.'.reviews')
		@include('site.elements.footer')
	@else
		<input type="hidden" name="website_link" id="website_link" value="{{ url('/') }}" />
    	<input type="hidden" name="website_lang" id="website_lang" value="{{ \App::getLocale() }}" />
	@endif

	@if(Route::current()->getName() == 'site.'.$currentLang.'.users.login' || Route::current()->getName() == 'site.'.$currentLang.'.users.forgot-password' || Route::current()->getName() == 'site.'.$currentLang.'.users.register' || Route::current()->getName() == 'site.'.$currentLang.'.users.delivery-address' || Route::current()->getName() == 'site.'.$currentLang.'.users.personal-details' || Route::current()->getName() == 'site.'.$currentLang.'.users.notifications' || Route::current()->getName() == 'site.'.$currentLang.'.users.change-user-password'|| Route::current()->getName() == 'site.'.$currentLang.'.help')
		@include('site.elements.popups')
	@endif
	
	</div>
    
	<script type="text/javascript" src="{{ asset('js/site/plugins.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/site/jquery.validate.min.js')}}"></script>
	<script type="text/javascript" src="https://media.payrexx.com/modal/v1/gateway.min.js"></script>
	@php
	if (in_array(App::getLocale(), Helper::WEBITE_LANGUAGES)) {
		$jsLang = App::getLocale();
	@endphp
	<script src="{{asset('js/site/development_site_'.$jsLang.'.js')}}?{{rand()}}"></script>
	@php
	}
	@endphp
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
	<script type="text/javascript" src="{{ asset('js/site/custom.js')}}"></script>
	<script type="text/javascript" src="{{ asset('js/site/datepicker-'.$jsLang.'.js')}}"></script>
	{{-- @if (\Session::get('pincode') == '' && Route::current()->getName() == 'site.'.$currentLang.'.home')) --}}
	@if (Cookie::get('pincode') == '' && Route::current()->getName() == 'site.'.$currentLang.'.home')
		<script type="text/javascript">
		$(document).ready(function() {
			// $('#pincode_modal').addClass('tt_modal_show');
            // $('body').addClass('tt_modal_open');
		});
		</script>
	@endif


<div class="tt_modal" id="stripe_modal">
    <div class="tt_modal_container">
        <div class="tt_modal_main">
            <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
            <div class="tt_modal_body">
                <div class="form_wrap form_box text-center ">
					<div class="tt_modal_header mb-3">Payment</div>
					<p>Pay for your order</p>
                    <form method="POST" autocomplete="off" action="" name="stripePaymentForm" id="stripePaymentForm" novalidate="" enctype="multipart/form-data">
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-12">
								<span class="stripe-payment-error"></span>
								<div class="field-container hide">
									<label for="name">Name</label>
									<input id="stripe_name" maxlength="20" name="name" type="text" placeholder="Card holder name">
								</div>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-12">
								<div class="field-container">
									<label for="cardnumber">@if($currentLang=='de') Kartennummer * @else Card Number @endif</label>
									<input id="stripe_cardnumber"  class="allownumericwithoutdecimal" maxlength="16" name="card_number" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="@if($currentLang=='de') Kartennummer * @else Card Number @endif" required>
								</div>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6">
								<div class="field-container">
									<label for="expirationdate">@if($currentLang=='de') Verfall * @else Expiry Date (mm/yy) @endif</label>
									<input id="stripe_expirationdate"  type="text" name="exp_date" pattern="[0-9]*" inputmode="numeric" placeholder="@if($currentLang=='de') Verfall * @else Expiry Date (mm/yy) @endif" required>
								</div>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6">
								<div class="field-container">
									<label for="securitycode">@if($currentLang=='de') CVC @else CVC @endif *</label>
									<input id="stripe_securitycode" name="cvc_number" maxlength="4" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="@if($currentLang=='de') CVC @else CVC @endif" required> 
								</div>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-12">
								<p class="text-left colorblack"></p>
								<button type="submit" class="btn btn-width">@if($currentLang=='de') Bezahlen <span class="final_amount_show">{{$cartAmount}}</span> CHF @else Pay CHF <span class="final_amount_show">{{$cartAmount}}</span> @endif</button>
								<div class="gray-box">
									<p class="">This is a secure 128-bit SSL encrypted payment.</p> 
									<img src="{{asset('images/site/payment1.png')}}" alt="">
								</div>
							</div>
						</div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

	<script type="text/javascript"> 
		$(document).on('click', '#click_to_popup', function() {
			$('#pincode_modal').addClass('tt_modal_show');
			$('body').addClass('tt_modal_open');
		});
	</script>

	@stack('stripe-payment')

	@if(Auth::user() && Route::current()->getName() == 'site.'.$currentLang.'.checkout' && Session::get('deliveryOption') == 'Delivery')
		@if (Auth::user()->userDeliveryAddresses->count() > 0)
			<script type="text/javascript">
			$(document).ready(function() {
				var selectedAddressId = $('input[name="addressAlias"]:checked').val();
				getSelectedAddressWiseDeliveryCharge(selectedAddressId);
			});
			$(document).on('click', '.addressAlias', function() {
				var clickedDeliveryAddressId = $(this).val();
				getSelectedAddressWiseDeliveryCharge(clickedDeliveryAddressId);
			})
			</script>
		@endif
	@endif

	{!! NoCaptcha::renderJs($jsLang) !!}

</body>
</html>
