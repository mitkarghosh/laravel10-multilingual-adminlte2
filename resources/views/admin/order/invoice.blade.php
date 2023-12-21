@extends('admin.layouts.app', ['title' => $panel_title])

	@section('content')

	@php $orderTotalPrice = $getOrderDetails['total_price']; @endphp

    <!-- Content Header (Page header) -->
    <section class="content-header">
      	<h1>
        	@lang('custom_admin.lab_invoice')
        	<small>#{{sprintf('%06d', $orderId)}}</small>
      	</h1>
      	<ol class="breadcrumb">
        	<li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        	<li><a href="{{route('admin.'.\App::getLocale().'.order.list')}}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> @lang('custom_admin.lab_order_list')</a></li>
        	<li class="active">{{ $page_title }}</li>
      	</ol>
    </section>

    <div class="pad margin no-print">
      	<div class="callout callout-info" style="margin-bottom: 0!important;">
        	<h4><i class="fa fa-info"></i> @lang('custom_admin.lab_invoice_note'):</h4>
        	@lang('custom_admin.lab_invoice_message').
      	</div>
    </div>

    <!-- Main content -->
    <section class="invoice">
      	<!-- title row -->
      	<div class="row">
        	<div class="col-xs-12">
          		<h2 class="page-header">
            		<i class="fa fa-globe"></i> {{$siteSettings->website_title}}
            		<small class="pull-right">@lang('custom_admin.lab_invoice_date'): {{date('d/m/Y')}}</small>
          		</h2>
        	</div>
      	</div>
      	<!-- info row -->
      	<div class="row invoice-info">
        	<div class="col-sm-4 invoice-col">
          		@lang('custom_admin.lab_invoice_from')
          		<address>
            		<strong>{{$siteSettings->website_title}}</strong><br>
            		{!! $siteSettings->address !!}<br>
				@if ($siteSettings->phone_no)
					@lang('custom_admin.lab_invoice_phone'): {{$siteSettings->phone_no}}<br>
				@endif
            		@lang('custom_admin.lab_invoice_email'): {{$siteSettings->from_email}}
				@if ($siteSettings->mwst_number != null)
					{!! '<br>'.$siteSettings->mwst_number !!}
				@endif
          		</address>
        	</div>
        	<div class="col-sm-4 invoice-col">
          		@lang('custom_admin.lab_invoice_to')
          		<address>
					<strong>{{$orderDetails['delivery_full_name']}}</strong><br>
					@if ($orderDetails['delivery_company'] != null)
						{!!$orderDetails['delivery_company'].'<br/>'!!}
					@endif
					@if ($orderDetails['delivery_door_code'] != null)
						{{$orderDetails['delivery_door_code'].', '}}
					@endif
					{{$orderDetails['delivery_street']}}
					@if ($orderDetails['delivery_floor'] != null)
						{{', '.$orderDetails['delivery_floor']}}
					@endif		
					<br/>
					{{$orderDetails['delivery_post_code'].', '.$orderDetails['delivery_city']}}<br/>
					@lang('custom_admin.lab_invoice_phone'): {{$orderDetails['delivery_phone_no']}}<br>
					@lang('custom_admin.lab_invoice_email'): {{$orderDetails['delivery_email']}}
				</address>
			</div>
			<div class="col-sm-4 invoice-col">
				<b>@lang('custom_admin.lab_invoice') #{{sprintf('%06d', $orderId)}}</b><br>
				<br>
				<b>@lang('custom_admin.lab_order_id'):</b> {{$orderDetails['unique_order_id']}}<br>
				<b>@lang('custom_admin.lab_order_ordered_on'):</b> {{date('d.m.Y H:i', strtotime($orderDetails['purchase_date']))}}<br>
				@if ($orderDetails['order_status'] == 'O')
					<b>@lang('custom_admin.dashboard_order_status')</b>:
					@if ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '0')
						@lang('custom_admin.lab_order_delivery_status_new')
					@elseif ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '1')
						@lang('custom_admin.lab_order_delivery_status_processing')
					@elseif ($orderDetails['status'] == 'D')
						@lang('custom_admin.lab_order_delivery_status_delivered')
					@elseif ($orderDetails['status'] == 'C')
						@lang('custom_admin.label_cancelled')
					@else
						NA
					@endif
					<br>
					<b>@lang('custom_admin.lab_order_delivery_time'):</b> 
					@if ($orderDetails['delivery_is_as_soon_as_possible'] == 'N')
						{{date('d.m.Y', strtotime($orderDetails['delivery_date'])).' '.date('H:i', strtotime($orderDetails['delivery_time']))}}
					@else
						@lang('custom_admin.label_as_soon_as_possible')
					@endif<br>
					<b>@lang('custom_admin.label_delivery_type'):</b> 
					@if ($orderDetails['delivery_type'] == 'Delivery')
						@lang('custom_admin.new_lab_order_delivery_time')
					@else
						@lang('custom_admin.new_lab_order_click_collect')
					@endif
					<br>
				@endif
			</div>
      	</div>
      	<!-- /.row -->
		<hr>

      	<!-- Table row -->
      	<div class="row">
	        <div class="col-xs-12 table-responsive">
          		<table class="table table-striped">
            		<thead>
            			<tr>
              				<th>@lang('custom_admin.lab_order_product_quantity')</th>
              				<th>@lang('custom_admin.lab_order_product')</th>
              				<th>@lang('custom_admin.lab_invoice_sub_total')</th>
            			</tr>
            		</thead>
            		<tbody>
					@foreach($getOrderDetails['product_details'] as $key => $val)
            			<tr>
              				<td>{{$val['quantity']}}</td>
              				<td>
							  	{{$val['title']}} @if($val['attribute'] != '') {{' - '.$val['attribute']}} @endif
								@php
								if (count($val['ingredients']) > 0) {
									echo '<br>(';
									$k = 1;	
									foreach ($val['ingredients'] as $value) {
										echo $value['quantity'].'x '.$value['title'];
										if ($k < count($val['ingredients'])) {
											echo ', ';
										}
										$k++;
									}
									echo ')';
								}
								
								if(count($val['menu']) > 0) {
									// echo '<br>(';
									echo '<br>';
									$c = 1;
									foreach ($val['menu'] as $valMenu) {
										$b = 1;
										// echo $valMenu['menu_title'].': ';
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
									// echo ')';
								}
								@endphp
							</td>
              				<td>CHF {{$val['total_price']}}</td>
            			</tr>
					@endforeach
            		</tbody>
          		</table>
        	</div>
      	</div>
      	<!-- /.row -->

      	<div class="row">
        	<!-- accepted payments column -->
        	<div class="col-xs-6">
          		<p class=""><strong>@lang('custom_admin.lab_payment_method'):</strong> 
				@if($orderDetails['payment_method'] == '0')
					@lang('custom_admin.lab_payment_pending')
				@elseif($orderDetails['payment_method'] == '1')
					@lang('custom_admin.lab_payment_cod')
				@elseif($orderDetails['payment_method'] == '2')
					@lang('custom_admin.lab_payment_stripe')
				@elseif($orderDetails['payment_method'] == '3')
					@lang('custom_admin.label_card_on_door')
				@else
					NA
				@endif
				</p>
			@if ($orderDetails['delivery_note'] != '')
				<p class=""><strong>@lang('custom_admin.lab_delivery_note'):</strong> {{$orderDetails['delivery_note']}}
			@endif
        	</div>
			<div class="col-xs-3">
				<p class=""><strong>@lang('custom.label_subtotal'): CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</strong></p>
		  	</div>
			<div class="col-xs-3">
				@if ($orderDetails['delivery_type'] == 'Delivery' && $orderDetails['delivery_charge'] > 0)
					@php $orderTotalPrice += $orderDetails['delivery_charge']; @endphp
					<p class=""><strong>@lang('custom_admin.lab_delivery_charge'): CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['delivery_charge'])}}</strong></p>
				@else
					&nbsp;
				@endif
			</div>
        	<!-- /.col -->
      	</div>
		@if ($orderDetails['payment_method'] == '2')
		  	@php $orderTotalPrice = $orderTotalPrice + $orderDetails['card_payment_amount']; @endphp
	  		<div class="row">
		  		<!-- accepted payments column -->
		  		<div class="col-xs-6">
					&nbsp;
		  		</div>
		  		<div class="col-xs-3">
			  		&nbsp;
		  		</div>
		  		<div class="col-xs-3">
					<p class=""><strong>@lang('custom.label_new_card_payment'): CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderDetails['card_payment_amount']))}}</strong></p>
		  		</div>
		  		<!-- /.col -->
			</div>
	  	@endif
		@if ($orderDetails['coupon_code'] != null)
		  @php $orderTotalPrice = $orderTotalPrice - $orderDetails['discount_amount']; @endphp
		<div class="row">
        	<!-- accepted payments column -->
        	<div class="col-xs-6">
          		&nbsp;
        	</div>
			<div class="col-xs-3">
				&nbsp;
			</div>
        	<div class="col-xs-3">
          		<p class=""><strong>@lang('custom.discount'): CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['discount_amount'])}}</strong></p>
        	</div>
        	<!-- /.col -->
      	</div>
		@endif
		
		<div class="row">
        	<!-- accepted payments column -->
        	<div class="col-xs-6">
          		&nbsp;
        	</div>
			<div class="col-xs-3">
				&nbsp;
			</div>
        	<div class="col-xs-3">
          		<p class=""><strong>@lang('custom.label_total'): CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderTotalPrice))}}</strong></p>
        	</div>
        	<!-- /.col -->
      	</div>		

      	<!-- /.row -->
		<hr>

      	<!-- this row will not appear when printing -->
      	<div class="row no-print">
        	<div class="col-xs-12">
          		<a href="{{route('admin.'.\App::getLocale().'.order.invoice-print', $orderId)}}" target="_blank" class="btn btn-primary pull-right"><i class="fa fa-print"></i> @lang('custom_admin.lab_order_invoice_print')</a>
        	</div>
      	</div>
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>

	@endsection
  