@extends('email_templates.layouts.app_email')
	@section('content')
	  
	@php
	$orderTotalPrice = 0;
	$orderTotalPrice = $orderDetails['total_price'];
	@endphp

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="color:#141414; font-size:15px;"> @lang('custom.label_hello') @lang('custom.label_administrator'),</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
			@lang('custom.message_admin_new_order_placed').<br><br>
			@lang('custom.label_order_no'): {{$getOrderData['unique_order_id']}}
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="55%">
				<table width="98%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td width="100%" colspan="3" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_customer_delivery_details'):
						</td>
					</tr>
					<tr>
						<td width="38%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_full_name')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="60%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_full_name']}}</td>
					</tr>
					<tr>
						<td width="38%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_email_address')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="60%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_email']}}</td>
					</tr>
					<tr>
						<td width="38%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_phone_number')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="60%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_phone_no']}}</td>
					</tr>
					<tr>
						<td width="38%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_delivery_address')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="60%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_street'].', '.$getOrderData['delivery_post_code'].' '.$getOrderData['delivery_city']}}</td>
					</tr>
				</table>
			</td>
			<td width="45%">
				<table width="98%" border="0" cellspacing="0" cellpadding="5" style="float: right">
					<tr>
						<td width="25%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.lab_delivery')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="73%" align="left" valign="top" style="line-height:20px;">
                        @if ($getOrderData['delivery_is_as_soon_as_possible'] == 'N')
                            {{date('d.m.Y', strtotime($getOrderData['delivery_date'])).' '.date('H:i', strtotime($getOrderData['delivery_time']))}}
                        @else
                            @lang('custom_admin.label_as_soon_as_possible')
                        @endif    
                        </td>
					</tr>
					<tr>
						<td width="25%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.label_delivery_type')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="73%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_type']}}</td>
					</tr>
				@if ($getOrderData['delivery_note'] != null)
					<tr>
						<td width="25%" align="left" valign="top" style="color:#141414; font-weight:normal; line-height:20px;">
							@lang('custom.mail_note')
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="73%" align="left" valign="top" style="line-height:20px;">{{$getOrderData['delivery_note']}}</td>
					</tr>
				@endif
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td width="100%" colspan="3" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">
							@lang('custom.mail_order_details'):
						</td>
					</tr>
				@foreach ($orderDetails['product_details'] as $item)
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">
							{{$item['title']}} @if($item['attribute'] != '') {{' - '.$item['attribute']}} @endif
							@php
							if(count($item['ingredients']) > 0) {
								echo '<br>(';
								$k = 1;	
								foreach ($item['ingredients'] as $val) {
									echo $val['title'];
									if ($k < count($item['ingredients'])) {
										echo ', ';
									}
									$k++;
								}
								echo ')';
							}
							
							if(count($item['menu']) > 0) {
								echo '<br>(';
								$c = 1;
								foreach ($item['menu'] as $valMenu) {
									$b = 1;
									echo $valMenu['menu_title'].': ';
									foreach ($valMenu['menu_value'] as $keyMenuValue => $valMenuValue) {
										if ($valMenu['menu_price'][$keyMenuValue] > 0) {
											echo $valMenuValue.' (+ '.$valMenu['menu_price'][$keyMenuValue].' CHF)';
										} else {
											echo $valMenuValue;
										}
										if ($b < count($valMenu['menu_value'])) {
											echo ', ';
										}
										$b++;
									}
									if ($c < count($item['menu'])) {
										echo '<br>';
									}
									$c++;
								}
								echo ')';
							}
							@endphp
						</td>
						<td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px;">CHF {{$item['total_price']}}</td>
					</tr>
				@endforeach
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.label_subtotal')</td>
						<td width="2%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc; font-weight: bold;">CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</td>
					</tr>
				@php
				if ($orderDetails['delivery_type'] == 'Delivery' && $orderDetails['delivery_charge'] > 0) {
					$orderTotalPrice += $orderDetails['delivery_charge'];
				@endphp
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.lab_delivery_charge')</td>
						<td width="2%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc; font-weight: normal;">CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['delivery_charge'])}}</td>
					</tr>
				@php
				}
				@endphp
				@php
				if ($orderDetails['payment_method'] == '2') {
					$orderTotalPrice += $orderDetails['card_payment_amount'];
				@endphp
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.label_new_card_payment')</td>
						<td width="2%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc; font-weight: normal;">CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['card_payment_amount'])}}</td>
					</tr>
				@php
				}
				@endphp
				@php
				if ($orderDetails['coupon_code'] != null) {
					$orderTotalPrice = $orderTotalPrice - $orderDetails['discount_amount'];
				@endphp
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.discount')</td>
						<td width="2%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc; font-weight: normal;">CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['discount_amount'])}}</td>
					</tr>
				@php
				}
				@endphp				
					<tr>
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.label_total')</td>
						<td width="2%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">:</td>
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc; font-weight: bold;">CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice))}}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>     
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="color:#141414; font-size:15px; line-height: 20px;">
				@lang('custom.label_email_thanks_regards'),<br>
				{{$siteSetting->website_title}}
				@if ($siteSetting->mwst_number != null)
					<br>{!! $siteSetting->mwst_number !!}
				@endif
			</td>
		</tr>
	</table>
    
  @endsection