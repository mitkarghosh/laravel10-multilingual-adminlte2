@extends('admin.layouts.invoice', ['title' => $panel_title])

@php $orderTotalPrice = $getOrderDetails['total_price']; @endphp

<!-- Main content -->
<section class="invoice invoiceSmall">
	<!-- title row -->
	<div class="row">
		<div class="col-xs-12">
			<h2 class="page-header">
				{{$siteSettings->website_title}}
				<small class="pull-right">{{--@lang('custom_admin.lab_invoice_date'): --}}{{date('d.m.Y')}}</small>
				<address>
					{!! $siteSettings->address !!}
					@if ($siteSettings->phone_no)
						{!! '<br>'.$siteSettings->phone_no !!}
						{{--@lang('custom_admin.lab_invoice_phone'): {{$siteSettings->phone_no}}--}}
					@endif
					{{--@lang('custom_admin.lab_invoice_email'): {{$siteSettings->from_email}}--}}
					@if ($siteSettings->mwst_number != null)
						{!! '<br>'.$siteSettings->mwst_number !!}
					@endif
				</address>
			</h2>
			<hr>
		</div>
	</div>
	<!-- /.row -->
	
	<!-- info row -->
	<div class="row invoice-info">
		<div class="col-xs-12 invoice-col">
			<div class="row invoice-col">
				<div class="col-xs-8">
					<div class="page-header small text-left">
						<strong class="sb">@lang('custom_admin.lab_order_customer')</strong>
						<address>
							{{$orderDetails['delivery_full_name']}}<br>
							@if ($orderDetails['delivery_company'] != null)
								{{$orderDetails['delivery_company']}}<br/>
							@endif
							@if ($orderDetails['delivery_door_code'] != null)
								{{$orderDetails['delivery_door_code'].', '}}
							@endif
							{{$orderDetails['delivery_street']}}
							@if ($orderDetails['delivery_floor'] != null)
								{{', '.$orderDetails['delivery_floor'].' floor'}}
							@endif
							<br/>
							{{$orderDetails['delivery_post_code'].' '.$orderDetails['delivery_city']}}<br/>
							{{$orderDetails['delivery_phone_no']}}
							{{-- @lang('custom_admin.lab_invoice_phone'): {{$orderDetails['delivery_phone_no']}} --}}
						</address>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="page-header text-right">
					@php
						if (($orderDetails['delivery_company'] != null)) {
							$company = str_replace(" ","+",$orderDetails['delivery_company']);
						}else{
							$company = '';
						}
						$street = str_replace(" ","+",$orderDetails['delivery_street']);
						$city = str_replace(" ","+",$orderDetails['delivery_city']);
						$full_address= "https://maps.google.com/maps?q=".$street."+".$orderDetails['delivery_post_code']."+".$city."+switzerland";
					@endphp
						<!-- <address class="mt-10">
							{!! QrCode::size(80)->generate($full_address) !!}
						</address> -->
					</div>
				</div>
			</div>
			
			<hr>
		</div>

		@if ($orderDetails['order_status'] == 'O')
			<div class="col-xs-12 invoice-col">
				<div class="page-header text-center">
					{{-- @lang('custom_admin.new_lab_order_delivery_time')<br> --}}
					@if ($orderDetails['delivery_type'] == 'Delivery')
						@lang('custom_admin.new_lab_order_delivery_time')
					@else
						@lang('custom_admin.new_lab_order_click_collect')
					@endif
					<br>
					@if ($orderDetails['delivery_is_as_soon_as_possible'] == 'N')
						{{date('d.m.Y', strtotime($orderDetails['delivery_date'])).' '.date('H:i', strtotime($orderDetails['delivery_time']))}}
					@else
						@lang('custom_admin.label_as_soon_as_possible')
					@endif
				</div>
				<hr>
			</div>
		@endif

		<div class="col-xs-12 invoice-col">
			<div class="page-header text-center">
				@lang('custom_admin.lab_order_id'): {{$orderDetails['unique_order_id']}}<br>
				
				<address>
					@lang('custom_admin.lab_order_ordered_on'): {{date('d.m.Y H:i', strtotime($orderDetails['purchase_date']))}}
					{{--@if ($orderDetails['order_status'] == 'O')
						<br>
						@lang('custom_admin.dashboard_order_status'):
						@if ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '0')
							@lang('custom_admin.lab_order_delivery_status_new')
						@elseif ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '1')
							@lang('custom_admin.lab_order_delivery_status_processing')
						@elseif ($orderDetails['status'] == 'D')
							@lang('custom_admin.lab_order_delivery_status_delivered')
						@else
							NA
						@endif
						,
						@lang('custom_admin.label_delivery_type'): {{$orderDetails['delivery_type']}}
					@endif--}}
				</address> 
			</div>
			<hr>
		</div>
	</div>
	<!-- /.row -->

	<!-- Table row -->
	<div class="row">
		<div class="col-xs-12 invoice-col">
			<div class="page-header text-center">@lang('custom_admin.lab_order_product')</div>
			<div class="table-responsive2">
				<table class="table">
					<tbody>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
					@php $categoryName  = ''; @endphp
					@foreach($getOrderDetails['product_details'] as $key => $val)
						@php
						if ($categoryName != $val['category_title']) {
						@endphp
						<tr>
							<td colspan="3"><strong>{{ $val['category_title'] }}</strong></td>
						</tr>
						@php
							$categoryName = $val['category_title'];
						}
						@endphp
						<tr>
							<td>{{$val['quantity']}}x</td>
							<td>
								{{$val['title']}} @if($val['attribute'] != '') {{' - '.$val['attribute']}} @endif
								@php
								if (count($val['ingredients']) > 0) {
									echo '<br>';
									$k = 1;	
									foreach ($val['ingredients'] as $value) {
										echo $value['quantity'].'x '.$value['title'];
										if ($k < count($val['ingredients'])) {
											echo ', ';
										}
										$k++;
									}
								}

								if (count($val['menu']) > 0) {
									echo '<br>';
									$c = 1;
									foreach ($val['menu'] as $valMenu) {
										$b = 1;
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
										if ($c < count($val['menu'])) {
											echo '<br>';
										}
										$c++;
									}
								}
								@endphp
							</td>
							<td class="text-right">CHF {{$val['total_price']}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<div class="clearfix" style="margin-bottom: 10px;">
				<span class="pull-left">@lang('custom.label_subtotal')</span>
				<span class="pull-right">CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</span>
			</div>
			@if ($orderDetails['delivery_type'] == 'Delivery' && $orderDetails['delivery_charge'] > 0)
				@php $orderTotalPrice += $orderDetails['delivery_charge']; @endphp
			<div class="clearfix" style="margin-bottom: 10px;">
				<span class="pull-left">@lang('custom_admin.lab_delivery_charge')</span>
				<span class="pull-right">CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['delivery_charge'])}}</span>
			</div>
			@endif
			@if ($orderDetails['payment_method'] == '2')
				@php $orderTotalPrice += $orderDetails['card_payment_amount']; @endphp
			<div class="clearfix" style="margin-bottom: 10px;">
				<span class="pull-left">@lang('custom.label_new_card_payment')</span>
				<span class="pull-right">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderDetails['card_payment_amount']))}}</span>
			</div>
			@endif
			@if ($orderDetails['coupon_code'] != null)
				@php $orderTotalPrice = $orderTotalPrice - $orderDetails['discount_amount']; @endphp
			<div class="clearfix" style="margin-bottom: 10px;">
				<span class="pull-left">@lang('custom_admin.lab_discount')</span>
				<span class="pull-right">CHF -{{Helper::formatToTwoDecimalPlaces($orderDetails['discount_amount'])}}</span>
			</div>
			@endif
			<div class="clearfix" style="margin-top: 16px;">
				<strong class="pull-left">@lang('custom.label_total')
				@if ($siteSettings['mwst_number'] != null)
					<br><small style="font-size: 10px;">(@lang('custom_admin.label_total_vat'))</small>
				@endif
														</strong>
				<strong class="pull-right">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice))}}</strong>
			</div>
			<hr>
		</div>
	</div>
	<!-- /.row -->

	<!-- accepted payments column -->
	<div class="row">
		@if ($orderDetails['delivery_note'] != '')
			<div class="col-xs-12 invoice-col">
				<div class="page-header small text-center">
					<strong class="sb">@lang('custom_admin.lab_delivery_note')</strong><br>
					<address>{{$orderDetails['delivery_note']}}</address>
				</div>
				<hr>
			</div>
		@endif

		<div class="col-xs-12 invoice-col">
			<div class="page-header small text-center">
				<strong class="sb">@lang('custom_admin.lab_payment_method')</strong><br>
				<address class="text_lg">
				@if($orderDetails['payment_method'] == '0')
					@lang('custom_admin.lab_payment_pending')
				@elseif($orderDetails['payment_method'] == '1')
					@lang('custom_admin.label_need_pay_cash')
				@elseif($orderDetails['payment_method'] == '2')
					@lang('custom_admin.label_pay_online')
				@elseif($orderDetails['payment_method'] == '3')
					@lang('custom_admin.label_card_on_door')
				@else
					NA
				@endif
				</address>
			</div>
<center><address class="mt-10">
{!! QrCode::size(125)->generate($full_address) !!}
</address>
</center>
		</div>
	</div>
	<!-- /.row -->
</section>
<!-- /.content -->