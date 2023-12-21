@extends('site.layouts.app', [])
	@section('content')

	@php
	$orderTotalPrice = 0;
	$orderTotalPrice = $orderDetails['total_price'];
	@endphp

	<main class="mainContainer">
		<section class="section order_details">
			<div class="container">
				<div class="row">
					<article class="col-sm-6 stickyContent @if ($getOrderDetails->status == 'D') pull-right col-xs-12 @else col-sm-offset-3 @endif">
						<div class="tt_box">
							<div class="resBox">
								<figure><img src="{{asset('images/site/logo.png')}}" alt=""></figure>
								<div>
									<h1 class="heading">@lang('custom.label_web_site_title')</h1>
									<div>{{$getOrderDetails->delivery_street.', '.$getOrderDetails->delivery_post_code.' '.$getOrderDetails->delivery_city}}</div>
								</div>
							</div>
							<div class="orderD">
								<h2 class="heading">@lang('custom.lab_your_order')</h2>
								<div>@lang('custom.label_order_no'): {{$getOrderDetails->unique_order_id}}</div>
								<div>@lang('custom.label_delivery_type'): @if ($getOrderDetails->delivery_type == 'Delivery')
									@lang('custom_admin.new_lab_order_delivery_time')
								@else
									@lang('custom_admin.new_lab_order_click_collect')
								@endif
								|
								@if ($getOrderDetails->delivery_is_as_soon_as_possible == 'N')
									{{date('d.m.Y', strtotime($getOrderDetails->delivery_date))." ".date('H:i', strtotime($getOrderDetails->delivery_time))}}
								@else
									@lang('custom_admin.label_as_soon_as_possible')
								@endif
								</div>
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
																// echo ', ';
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
								<div class="border_top">
									<div><a href="{{route('site.'.\App::getLocale().'.users.invoice-print', Helper::customEncryptionDecryption($getOrderDetails->id))}}" target="_blank" class="pull-right"><i class="fa fa-print"></i> @lang('custom_admin.lab_order_invoice_print')</a></div>
								</div>
							</div>
						</div>
					</article>

				@if ($getOrderDetails->status == 'D' && $orderReviewDetails == null)
					<aside class="col-sm-6 col-xs-12 stickySidebar orderSidebar">
						<div class="tt_box">
							<div class="form_wrap">
								@include('site.elements.notification')

								<h2 class="heading">@lang('custom.label_how_was_your_order')</h2>
								{{ Form::open(array(
												'method'=> 'POST',
												'class' => '',
												'route' => ['site.'.\App::getLocale().'.users.order-review-submit'],
												'name'  => 'orderReviewForm',
												'id'    => 'orderReviewForm',
												'files' => true,
												'autocomplete' => false,
												'novalidate' => true)) }}
									<input type="hidden" name="order_id" id="order_id" value="{{$id}}">
									<ul class="row">
										<li class="col-sm-12">
											<div class="form-group">
												<label>
													<span>@lang('custom.label_food_quality')</span>
													<div class="rating_wrap give_rating">
														<div class="rating">
															<i class="fa fa-star-o rated" id="1"></i>
															<i class="fa fa-star-o rated" id="2"></i>
															<i class="fa fa-star-o rated" id="3"></i>
															<i class="fa fa-star-o rated" id="4"></i>
															<i class="fa fa-star-o rated" id="5"></i>
														</div>
														<input type="hidden" placeholder="" name="food_quality" id="food_quality" value="5">
													</div>
												</label>
												<div class="help_block"></div>
											</div>
										</li>
										<li class="col-sm-12">
											<div class="form-group">
												<label>
													<span>@lang('custom.labe_delivery_time')</span>
													<div class="rating_wrap give_rating">
														<div class="rating">
															<i class="fa fa-star-o rated" id="1"></i>
															<i class="fa fa-star-o rated" id="2"></i>
															<i class="fa fa-star-o rated" id="3"></i>
															<i class="fa fa-star-o rated" id="4"></i>
															<i class="fa fa-star-o rated" id="5"></i>
														</div>
														<input type="hidden" name="delivery_time" id="delivery_time" placeholder="" value="5">
													</div>
												</label>
												<div class="help_block"></div>
											</div>
										</li>
										<li class="col-sm-12">
											<div class="form-group">
												<label>
													<span>@lang('custom.label_driver_friendliness')</span>
													<div class="rating_wrap give_rating">
														<div class="rating">
															<i class="fa fa-star-o rated" id="1"></i>
															<i class="fa fa-star-o rated" id="2"></i>
															<i class="fa fa-star-o rated" id="3"></i>
															<i class="fa fa-star-o rated" id="4"></i>
															<i class="fa fa-star-o rated" id="5"></i>
														</div>
														<input type="hidden" name="driver_friendliness" id="driver_friendliness" placeholder="" value="5">
													</div>
												</label>
												<div class="help_block"></div>
											</div>
										</li>
										<li class="col-sm-12">
											<div class="form-group">
												<label class="labelWrap1">
													<span>@lang('custom.label_leave_a_short_review')</span>
													<div class="mb5">@lang('custom.label_review_name') {{$getOrderDetails->delivery_full_name}}</div>
													<textarea name="short_review" id="short_review" placeholder="@lang('custom.label_leave_a_short_review')"></textarea>
												</label>
												<div class="help_block"></div>
											</div>
										</li>
										<li class="col-sm-12 text-center">
											<button type="submit" class="btn">@lang('custom.btn_submit_review')</button>
										</li>
									</ul>
								{{Form::close()}}
							</div>
						</div>
					</aside>
				@elseif ($orderReviewDetails != null)
					<aside class="col-sm-6 col-xs-12 stickySidebar orderSidebar">
						<div class="tt_box">
							<div class="form_wrap">
								@include('site.elements.notification')
								
								<h2 class="heading">@lang('custom.label_how_was_your_order')</h2>
								<ul class="row">
									<li class="col-sm-12">
										<div class="form-group">
											<label>
												<span>@lang('custom.label_food_quality')</span>
												<div class="rating_wrap given_rating">
													<div class="rating">
													@php
													for ($foodQuality = 1; $foodQuality <= 5; $foodQuality++) {
														if ($foodQuality <= $orderReviewDetails->food_quality) {
															echo '<i class="fa fa-star"></i>';													
														} else {
															echo '<i class="fa fa-star-o"></i>';
														}
													}
													@endphp
													</div>
												</div>
											</label>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label>
												<span>@lang('custom.labe_delivery_time')</span>
												<div class="rating_wrap given_rating">
													<div class="rating">
													@php
													for ($deliveryTime = 1; $deliveryTime <= 5; $deliveryTime++) {
														if ($deliveryTime <= $orderReviewDetails->delivery_time) {
															echo '<i class="fa fa-star"></i>';
														} else {
															echo '<i class="fa fa-star-o"></i>';
														}
													}
													@endphp
													</div>
												</div>
											</label>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label>
												<span>@lang('custom.label_driver_friendliness')</span>
												<div class="rating_wrap given_rating">
													<div class="rating">
													@php
													for ($driverFriendliness = 1; $driverFriendliness <= 5; $driverFriendliness++) {
														if ($driverFriendliness <= $orderReviewDetails->driver_friendliness) {
															echo '<i class="fa fa-star"></i>';
														} else {
															echo '<i class="fa fa-star-o"></i>';
														}
													}
													@endphp
													</div>
												</div>
											</label>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_leave_a_short_review')</span>
												<div>{!! $orderReviewDetails->short_review !!}</div>
											</label>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</aside>
				@endif
				</div>
			</div>
		</section>
	</main>

	@endsection