@extends('email_templates.layouts.app_email')
  	@section('content')

	@php
	$orderTotalPrice = 0;
	$orderTotalPrice = $orderDetails['total_price'];
	@endphp

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      	<tr>
        	<td style="color:#141414; font-size:15px;"> @lang('custom_admin.label_hello') {{ $user->first_name }},</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>
				{{ trans('custom_admin.message_your_order', ['orderid' => $details->unique_order_id]) }}.
        	</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
			<td>
				@lang('custom.message_below_are_the_order_details')<br><br>
				@lang('custom.label_order_no'): {{$details->unique_order_id}}<br><br>
				@lang('custom.lab_delivery'): 
				@if ($details->delivery_is_as_soon_as_possible == 'N')
                    {{date('d.m.Y', strtotime($details->delivery_date)).' '.date('H:i',strtotime($details->delivery_time))}}
                @else
                    @lang('custom_admin.label_as_soon_as_possible')
                @endif
				<br><br>
				@lang('custom_admin.lab_payment_method'): 
				@if($details->payment_method == '0')
					@lang('custom_admin.lab_payment_pending')
				@elseif($details->payment_method == '1')
					@lang('custom_admin.lab_payment_cod')
				@elseif($details->payment_method == '2')
					@lang('custom_admin.lab_payment_stripe')
				@elseif($details->payment_method == '3')
					@lang('custom_admin.label_card_on_door')
				@else
					NA
				@endif
				<br><br>
				@if ($details->order_status == 'O')
					@lang('custom_admin.lab_order_delivery_status'): 
					@if ($details->status == 'P' && $details->is_print == '0')
						@lang('custom_admin.lab_order_delivery_status_new')
					@elseif ($details->status == 'P' && $details->is_print == '1')
						@lang('custom_admin.lab_order_delivery_status_processing')
					@elseif ($details->status == 'D')
						@lang('custom_admin.lab_order_delivery_status_delivered')
					@elseif ($details->status == 'C')
						@lang('custom_admin.label_cancelled')
					@else
						NA
					@endif
				@endif
				<br><br>
				@lang('custom.label_delivery_type'): 
				@if ($details->delivery_type == 'Delivery')
					@lang('custom_admin.new_lab_order_delivery_time')
				@else
					@lang('custom_admin.new_lab_order_click_collect')
				@endif
				<br><br>
				@lang('custom_admin.lab_order_delivery_address'): 
				@if ($details->delivery_company != null)
					{!! $details->delivery_company.', ' !!}
				@endif
				@if ($details->delivery_door_code != null)
					{{$details->delivery_door_code.', '}}
				@endif
				{{$details->delivery_street.', '}}
				@if ($details->delivery_floor != null)
					{{$details->delivery_floor.', '}}
				@endif
				{{$details->delivery_post_code.' '.$details->delivery_city}}
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
						if (count($item['ingredients']) > 0) {
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
						
						if (count($item['menu']) > 0) {
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
						<td width="25%" align="left" valign="top" style="line-height:20px; border-top: 1px solid #ccc;">CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</td>
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
						<td width="73%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px; border-top: 1px solid #ccc;">@lang('custom.label_card_payment')</td>
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