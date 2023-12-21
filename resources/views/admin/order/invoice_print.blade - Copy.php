@extends('admin.layouts.invoice', ['title' => $panel_title])

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
            		{{$siteSettings->address}}<br>
				@if ($siteSettings->phone_no)
					@lang('custom_admin.lab_invoice_phone'): {{$siteSettings->phone_no}}<br>
				@endif
            		@lang('custom_admin.lab_invoice_email'): {{$siteSettings->from_email}}
          		</address>
        	</div>
        	<div class="col-sm-4 invoice-col">
          		@lang('custom_admin.lab_invoice_to')
          		<address>
					<strong>{{$orderDetails['delivery_full_name']}}</strong><br>
					{{$orderDetails['delivery_company']}}<br/>
					@if ($orderDetails['delivery_door_code'] != null)
						{{$orderDetails['delivery_door_code'].', '}}
					@endif
					{{$orderDetails['delivery_street']}}
					@if ($orderDetails['delivery_floor'] != null)
						{{', '.$orderDetails['delivery_floor']}}
					@endif
					<br/>
					{{$orderDetails['delivery_post_code'].', '.$orderDetails['delivery_city']}}<br/>
					@lang('custom_admin.lab_invoice_phone'): {{env('COUNTRY_CODE','+49').$orderDetails['delivery_phone_no']}}<br>
					@lang('custom_admin.lab_invoice_email'): {{$orderDetails['delivery_email']}}
				</address>
			</div>
			<div class="col-sm-4 invoice-col">
				<b>@lang('custom_admin.lab_invoice') #{{sprintf('%06d', $orderId)}}</b><br>
				<br>
				<b>@lang('custom_admin.lab_order_id'):</b> {{$orderDetails['unique_order_id']}}<br>
				<b>@lang('custom_admin.lab_order_ordered_on'):</b> {{date('d/m/Y H:i', strtotime($orderDetails['purchase_date']))}}<br>
				@if ($orderDetails['order_status'] == 'O')
					<b>@lang('custom_admin.dashboard_order_status')</b>:
					@if ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '0')
						@lang('custom_admin.lab_order_delivery_status_new')
					@elseif ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '1')
						@lang('custom_admin.lab_order_delivery_status_processing')
					@elseif ($orderDetails['status'] == 'D')
						@lang('custom_admin.lab_order_delivery_status_delivered')
					@else
						NA
					@endif
					<br>
					<b>@lang('custom_admin.lab_order_delivery_time'):</b> {{date('d/m/Y', strtotime($orderDetails['delivery_date'])).' '.date('H:i', strtotime($orderDetails['delivery_time']))}}<br>
					<b>@lang('custom_admin.label_delivery_type'):</b> {{$orderDetails['delivery_type']}}<br>
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
            			<tr style="font-size: 14px;">
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

								if (count($val['menu']) > 0) {
									echo '<br>(';
									$c = 1;
									foreach ($val['menu'] as $valMenu) {
										$b = 1;
										echo $valMenu['menu_title'].': ';
										foreach ($valMenu['menu_value'] as $valMenuValue) {
											echo $valMenuValue;
											if ($b < count($valMenu['menu_value'])) {
												echo ', ';
											}
											$b++;
										}
										if ($c < count($val['menu'])) {
											echo '<br>';
										}
										$c++;
									}
									echo ')';
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
				{{-- <img src="../../dist/img/credit/visa.png" alt="Visa">
				<img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
				<img src="../../dist/img/credit/american-express.png" alt="American Express">
				<img src="../../dist/img/credit/paypal2.png" alt="Paypal"> --}}
			@if ($orderDetails['delivery_note'] != '')
				<p class=""><strong>@lang('custom_admin.lab_delivery_note'):</strong> {{$orderDetails['delivery_note']}}
			@endif
        	</div>
			<div class="col-xs-3">
        		&nbsp;
			</div>
        	<div class="col-xs-3">
          		<p class=""><strong>@lang('custom_admin.lab_order_total'): CHF {{$getOrderDetails['total_price']}}</strong></p>
        	</div>
        	<!-- /.col -->
      	</div>
      	<!-- /.row -->		
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
