@extends('site.layouts.invoice', [])
	@section('content')

	@php
	$orderTotalPrice = 0;
	$orderTotalPrice = $orderDetails['total_price'];
	@endphp

	<main class="mainContainer invoicePrint">
		<section class="section order_details">
			<div class="container">
				<div class="row">
					<article class="col-sm-6 col-sm-offset-3">
						<div class="tt_box">
							<div class="resBox">
								<figure><img src="{{asset('images/site/logo.png')}}" alt=""></figure>
								<div>
									<h1 class="heading">@lang('custom.label_web_site_title')</h1>
									<div>{{$getOrderDetails->delivery_street.', '.$getOrderDetails->delivery_post_code.', '.$getOrderDetails->delivery_city}}</div>
								</div>
							</div>
							<div class="orderD">
								<h2 class="heading">@lang('custom.lab_your_order')</h2>
								<div>@lang('custom.label_order_no'): {{$getOrderDetails->unique_order_id}}</div>
								<div>@lang('custom.label_delivery_type'): {{$getOrderDetails->delivery_type}}</div>
								<div class="border_top">
								@if (count($orderDetails['product_details']))
									<ul>
									@foreach ($orderDetails['product_details'] as $item)
										<li>
											<strong class="tt_fleft tqty">{{$item['quantity']}}x</strong>
											<span class="tt_fleft">
												<strong>{{$item['title']}} @if($item['attribute'] != '') {{' - '.$item['attribute']}} @endif</strong>
												@php
												if(count($item['ingredients']) > 0) {
													echo '<br>';
													$k = 1;	
													foreach ($item['ingredients'] as $val) {
														echo $val['title'];
														if ($k < count($item['ingredients'])) {
															echo ', ';
														}
														$k++;
													}
												}

												if (count($item['menu']) > 0) {
													echo '<br>';
													$c = 1;
													foreach ($item['menu'] as $valMenu) {
														$b = 1;
														// echo '+ '.$valMenu['menu_title'].': ';
														echo '+ ';
														foreach ($valMenu['menu_value'] as $keyMenuValue => $valMenuValue) {
															if (isset($valMenu['menu_price'][$keyMenuValue]) && $valMenu['menu_price'][$keyMenuValue] > 0) {
																// echo $valMenuValue.' (+ '.$valMenu['menu_price'][$keyMenuValue].' CHF)';
																echo $valMenuValue;
															} else {
																echo $valMenuValue;
															}
															if ($b < count($valMenu['menu_value'])) {
																// echo '+ ';
																echo '<br>+ ';
															}
															$b++;
														}
														if ($c < count($item['menu'])) {
															echo '<br>';
														}
														$c++;
													}
												}
												@endphp
											</span>
											<strong class="tt_fright">CHF {{$item['total_price']}}</strong>
										</li>
									@endforeach
									</ul>
								@endif
								</div>
								<div class="border_top">
									<ul>
										<li>
											<span class="tt_fleft">@lang('custom.label_subtotal')</span>
											<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</strong>
										</li>
									@php
									if ($orderDetails['delivery_type'] == 'Delivery' && $orderDetails['delivery_charge'] > 0) {
										$orderTotalPrice += $orderDetails['delivery_charge'];
									@endphp
										<li>
											<span class="tt_fleft">@lang('custom.lab_delivery_charge')</span>
											<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['delivery_charge'])}}</strong>
										</li>
									@php
									}
									if ($orderDetails['payment_method'] == '2') {
										$orderTotalPrice += $orderDetails['card_payment_amount'];
									@endphp
										<li>
											<span class="tt_fleft">@lang('custom.label_new_card_payment')</span>
											<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderDetails['card_payment_amount']))}}</strong>
										</li>
									@php
									}
									@endphp										
									@php
									if ($orderDetails['coupon_code'] != null) {
										$orderTotalPrice = $orderTotalPrice - $orderDetails['discount_amount'];
									@endphp
										<li>
											<span class="tt_fleft">@lang('custom.discount') ({!! $orderDetails['coupon_code'] !!})</span>
											<strong class="tt_fright">CHF -{{Helper::formatToTwoDecimalPlaces($orderDetails['discount_amount'])}}</strong>
										</li>
									@php
									}
									@endphp
										<li>
											<span class="tt_fleft">@lang('custom.label_total')</span>
											<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice))}}</strong>
										</li>
									</ul>
								</div>
								@if ($getOrderDetails->delivery_note != null)
								<div class="border_top">
									<div><strong>@lang('custom.label_your_note')</strong></div>
									<div class="mt10">{{$getOrderDetails->delivery_note}}</div>
								</div>
								@endif
							</div>
						</div>
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection