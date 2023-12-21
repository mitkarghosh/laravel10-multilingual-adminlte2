@extends('site.layouts.app', [])
	@section('content')

	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')

						<h1 class="heading heading_medium b-b">@lang('custom.lab_orders_reviews')</h1>
					@if (count($orderList) > 0)
						<ul class="ul border_list">
						@foreach ($orderList as $key => $val)
							@php
							// dd($val);

							$orderTotalPrice = 0;
							$orderTotalPrice = $val['total_price'];
							if ($val['delivery_type'] == 'Delivery' && $val['delivery_charge'] > 0) {
								$orderTotalPrice += $val['delivery_charge'];
							}
							if ($val['coupon_code'] != null) {
								$orderTotalPrice = $orderTotalPrice - $val['discount_amount'];
							}
							if ($val['payment_method'] == '2') {
								$orderTotalPrice += $val['card_payment_amount'];
							}
							@endphp
							<li>
								<div class="orderBox">
									<div class="obLeft">
										<figure><img src="{{asset('images/site/logo.png')}}" alt=""></figure>
										<div>
											<h2 class="heading">@lang('custom.label_web_site_title')</h2>
											<div>@lang('custom.lab_delivery') {{date('d.m.Y', strtotime($val['delivery_date'])).' '.date('H:i', strtotime($val['delivery_time']))}}</div>
											<a href="{{route('site.'.\App::getLocale().'.users.order-details', $val['order_id'])}}" class="mt15">@lang('custom.label_view_this_order')</a>
										</div>
									</div>
									<div class="obRight">
									@if (count($val['product_details']) > 0)
										<ul>
										@foreach ($val['product_details'] as $product)
											<li>{{$product['quantity']}} x {{$product['product_title']}}</li>
										@endforeach
										</ul>
									@endif
										<div class="orderPrice">CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</div>
										<a href="{{route('site.'.\App::getLocale().'.users.order-details', $val['order_id'])}}" class="btn btn_blue_light">@lang('custom.label_view_details')</a>
									</div>
								</div>
							</li>
						@endforeach
						</ul>
					@else
						<div class="infoBox text-center">
							<figure><img src="{{asset('images/site/empty-order.png')}}" alt=""></figure>
							<h2 class="heading">@lang('custom.label_no_order_yet')</h2>
							<h3 class="subheading">@lang('custom.message_to_place_order')</h3>
							<a href="{{route('site.'.\App::getLocale().'.home')}}" class="btn">@lang('custom.label_order_now')!</a>
						</div>
					@endif

					@if(count($orderList)>0)
						<div class="pagination">						
							{{ $myOrderList->appends(request()->input())->links() }}
						</div>
					@endif					
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection