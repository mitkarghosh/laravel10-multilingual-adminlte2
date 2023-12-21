@extends('site.layouts.app', [])
	@section('content')
	@php
	$orderTotalPrice = 0;
	$orderTotalPrice = $orderDetails['total_price'];
	$siteSettings   = Helper::getSiteSettings();
	@endphp

	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					<article class="col-md-12">
						<div class="infoBox text-center">
							<figure><img src="{{asset('images/site/empty-order.png')}}" alt=""></figure>
							<h1 class="heading">@lang('custom.message_thank_you_order').</h1>
							<h2 class="subheading">{{trans('custom.message_order_brief', ['uniqueOrderId' => $uniqueOrderId])}}.</h2>

							{{-- Order Details Start --}}
							<div class="row">
								<article class="col-sm-6 col-sm-offset-3 mt15">
									<div class="tt_box">
										<div class="orderD">
											<h2 class="heading">@lang('custom.mail_order_details')</h2>
										@if (count($orderDetails['product_details']))
											<div class="border_top text-left">
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
																		if ($valMenu['menu_price'][$keyMenuValue] > 0) {
																			echo $valMenuValue;
																		} else {
																			echo $valMenuValue;
																		}
																		if ($b < count($valMenu['menu_value'])) {
																			// echo ', ';
																			echo '<br>+ ';
																		}
																		$b++;
																	}
																	if ($c < count($item['menu'])) {
																		echo '<br>';
																	}
																}
															}
															@endphp
														</span>
														<strong class="tt_fright">CHF {{$item['total_price']}}</strong>
													</li>
												@endforeach
												</ul>
											</div>
										@endif
											
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
												@endphp
													
												@php
												if ($orderDetails['payment_method'] == '2') {
													$orderTotalPrice += $orderDetails['card_payment_amount'];
												@endphp
													<li>
														<span class="tt_fleft">@lang('custom.label_new_card_payment')</span>
														<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderDetails['card_payment_amount']))}}</strong>
													</li>
												@php
												}
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
														<span class="tt_fleft" style="text-align: left;">
															@lang('custom.label_total')
															@if ($siteSettings['mwst_number'] != null)
																<br><small style="font-size: 10px;">(@lang('custom_admin.label_total_vat'))</small>
															@endif
														</span>
														<strong class="tt_fright">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice))}}</strong>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</article>
							</div>
							{{-- Order Details End --}}

						@if (!Auth::user())
							<a href="{{route('site.'.\App::getLocale().'.home')}}" class="btn">@lang('custom.label_continue_shopping')</a>
						@else
							<a href="{{route('site.'.\App::getLocale().'.users.orders-reviews')}}" class="btn">@lang('custom.lab_orders_reviews')</a>
						@endif
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection