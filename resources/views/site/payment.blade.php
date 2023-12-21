@extends('site.layouts.app', [])
  	@section('content')
        
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="form_wrap form_box">
                        @include('site.elements.notification')
                    @php
                    $payment_setting    = Helper::getPaymentSettings();
                    $stripe_method=!empty($payment_setting)?$payment_setting->stripe_method:''; 
                    @endphp
                    {{-- <form action="{{route('site.'.\App::getLocale().'.payment-process-stripe')}}" method="POST" name="stripeForm">
                        {!! csrf_field() !!}
                        <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="@if($stripe_method=='Y'){{$payment_setting->stripe_publish_key}}@else{{env('STRIPE_API_KEY')}}@endif"
                            data-amount="{{$cartDetails['totalCartPrice'] * 100}}"
                            data-name="{{$getOrderData['delivery_full_name']}}"
                            data-description="Schiff Binningen"
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-locate="auto"
                            data-currency="{{env('WEBSITE_CURRENCY', 'CHF')}}">
                        </script>
                    </form> --}}
                    
                </div>
			</div>
		</section>
    </main>

<div class="tt_modal" id="stripe_modal">
    <div class="tt_modal_container">
        <div class="tt_modal_main">
            <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
            <!-- <div class="tt_modal_header">Card Detail</div>
			<p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p> -->
            <div class="tt_modal_body">
                <div class="form_wrap form_box text-center ">
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
									<label for="cardnumber">Card Number</label>
									<input id="stripe_cardnumber" name="card_number" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="Card number">
								</div>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6">
								<div class="field-container">
									<label for="expirationdate">Expiration (mm/yy)</label>
									<input id="stripe_expirationdate"  type="text" name="exp_date" pattern="[0-9]*" inputmode="numeric" placeholder="mm/yy">
								</div>
							</div>
							<div class="col-sm-12 col-md-6 col-lg-6">
								<div class="field-container">
									<label for="securitycode">Security Code</label>
									<input id="stripe_securitycode" name="cvc_number" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="Security Code">
								</div>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-12">
								<button type="submit" class="btn btn-width">Submit</button>
							</div>
						</div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    
    @endsection