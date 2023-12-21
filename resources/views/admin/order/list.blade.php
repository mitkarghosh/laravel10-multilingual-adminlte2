@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $page_title }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <!-- Start: Filter Section -->
					@php
					$totalAmountPageWise = 0;
                    $cashPayment = $onlinePayment = $cardPayment = $cancelledPayment = $allTotalPayment = 0;
                    $purchaseDate = (isset($data['purchaseDate'])) ? $data['purchaseDate']:'';
                    $searchText = (isset($data['searchText'])) ? $data['searchText']:'';
                    $paymentMethod = (isset($data['payment_method'])) ? $data['payment_method'] : '';
                    $paymentStatus = (isset($data['payment_status'])) ? $data['payment_status'] : '';
                    $status = (isset($data['status'])) ? $data['status'] : '';

                    $requestParameters = app('request')->request->all();
                    @endphp
                    <div class="box-header">
                    {{ Form::open(array(
                                      'method' => 'GET',
                                      'class' => '',
                                      'route' =>  ['admin.'.\App::getLocale().'.order.list'],
                                      'id' => '',
                                      'novalidate' => true)) }}                    
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="purchase_date">@lang('custom_admin.lab_order_purchase_date'):</label>
                              <div class="input-group">
                                  <div class="input-group-addon">
                                      <i class="fa fa-clock-o"></i>
                                  </div>
                                  {{ Form::text('purchase_date',$purchaseDate, array(
                                    'id' => 'purchase_date',
                                    'class' => 'form-control',
                                    'autocomplete' => 'off' )) }}
                              </div>
                              @if($purchaseDate == '')
                                <div class="lab_report_download_text" style="color:red; display:none">@lang('custom_admin.lab_report_download_text')</div>
                              @endif
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="searchText">@lang('custom_admin.lab_order_search'):</label>
                                {{ Form::text('searchText', $searchText, array(
                                                'id' => 'searchText',
                                                'placeholder' => trans('custom_admin.label_search_by_in_order'),
                                                'class' => 'form-control')) }}
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="box-tools">
                              <div class="form-group">
                                <label for="payment_method">@lang('custom_admin.lab_payment_method'):</label>
                                <select name="payment_method[]" id="payment_method" multiple="multiple" class="form-control select2">
                                    <option value="0" {{(isset($data['payment_method']) && in_array('0',$data['payment_method'])) ? 'selected' : ''}}>@lang('custom_admin.lab_payment_pending')</option>
                                    <option value="1" {{(isset($data['payment_method']) && in_array('1',$data['payment_method'])) ? 'selected' : ''}}>@lang('custom_admin.lab_payment_cod')</option>
									<option value="2" {{(isset($data['payment_method']) && in_array('2',$data['payment_method'])) ? 'selected' : ''}}>@lang('custom_admin.lab_payment_stripe')</option>
									<option value="3" {{(isset($data['payment_method']) && in_array('3',$data['payment_method'])) ? 'selected' : ''}}>@lang('custom_admin.label_card_on_door')</option>
                                </select>
                              </div>
                          </div>
                        </div>                        
                      </div>
                    
                      <div class="row">
                        <div class="col-sm-4">
                          <div class="box-tools">
                              <div class="form-group">
                                <label for="payment_status">@lang('custom_admin.lab_order_payment_status'):</label>
                                <select name="payment_status[]" id="payment_status" multiple="multiple" class="form-control select2">
                                  <option value="P" {{(isset($data['payment_status']) && in_array('P',$data['payment_status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_payment_pending')</option>
                                  <option value="C" {{(isset($data['payment_status']) && in_array('C',$data['payment_status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_payment_completed')</option>
                                </select>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="box-tools">
                              <div class="form-group">
                                <label for="status">@lang('custom_admin.lab_order_delivery_status'):</label>
                                <select name="status[]" multiple="multiple" id="status" class="form-control select2">
                                    <option value="P-0" {{(isset($data['status']) && in_array('P-0',$data['status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_delivery_status_new')</option>
									                  <option value="P-1" {{(isset($data['status']) && in_array('P-1',$data['status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_delivery_status_processing')</option>
                                    <option value="D-1" {{(isset($data['status']) && in_array('D-1',$data['status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_delivery_status_delivered')</option>
                                    <option value="C-0" {{(isset($data['status']) && in_array('C-0',$data['status'])) ? 'selected' : ''}}>@lang('custom_admin.lab_order_delivery_status_cancelled')</option>
                                </select>
                              </div>
                          </div>
                        </div>
                      </div>
                  
                      <div class="row">
                        <!-- Filter section start -->
                        <div class="col-md-7">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.lab_order_filter')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.order.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.lab_order_reset')</a>
                        </div>
                        @if(count($orders) > 0 && $purchaseDate != '')
                        <div class="col-md-5 text-right">
                            <a href="{{ route('admin.'.\App::getLocale().'.order.export-to-excel', $requestParameters) }}" class="btn btn-warning">@lang('custom_admin.label_export_to_excel')</a>
                            &nbsp;
                            <a href="{{ route('admin.'.\App::getLocale().'.order.export-to-pdf', $requestParameters) }}" class="btn btn-success">@lang('custom_admin.label_export_to_pdf')</a>
                        </div>
                        @endif
                        @if($purchaseDate == '')
                        <div class="col-md-5 text-right">
                            <a href="javascript:void(0)" class="btn btn-warning dwnloadbtn">@lang('custom_admin.label_export_to_excel')</a>
                            &nbsp;
                            <a href="javascript:void(0)" class="btn btn-success dwnloadbtn">@lang('custom_admin.label_export_to_pdf')</a>
                        </div>
                        @endif
                        <!-- Filter section end -->
                      </div>
                    {!! Form::close() !!}
                    </div>
                    <!-- End: Filter Section -->                    
                </div>

                @include('admin.elements.notification')

                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('custom_admin.lab_order_id')</th>
                            <th>@lang('custom_admin.lab_order_customer_name')</th>
                            <th>@lang('custom_admin.lab_payment_method')</th>
                            <th>@lang('custom_admin.lab_order_payment_status')</th>
                            <th>@lang('custom_admin.dashboard_order_on')</th>
                            <th>@lang('custom_admin.lab_order_delivery_time')</th>
							<th>@lang('custom_admin.label_delivery_type')</th>
							<th>@lang('custom_admin.lab_order_total')</th>
                            <th>@lang('custom_admin.lab_order_delivery_status')</th>
                            <th class="action_width" style="text-align:center">@lang('custom_admin.lab_action')</th>
                        </tr>                        
                      @if(count($orders) > 0)
						@foreach ($orders as $key => $row)
							@php
							$orderTotalPrice = $row->orderDetails->sum('total_price');
							if ($row->delivery_type == 'Delivery' && $row->delivery_charge > 0) {
								$orderTotalPrice += $row->delivery_charge;
							}
							if ($row->coupon_code != null) {
								$orderTotalPrice = $orderTotalPrice - $row->discount_amount;
							}
							if ($row->payment_method == '2') {
								$orderTotalPrice += $row->card_payment_amount;
							}
							@endphp
						<tr>
                            <td>{{ $row['unique_order_id'] }}</td>
                            <td>{{ optional($row->userDetails)->full_name }}</td>
                            <td>
							@if($row->payment_method == '0')
                                @lang('custom_admin.lab_payment_pending')
							@elseif($row->payment_method == '1')
                                @lang('custom_admin.lab_payment_cod')
                            @elseif($row->payment_method == '2')
								@lang('custom_admin.lab_payment_stripe')
							@elseif($row->payment_method == '3')
                                @lang('custom_admin.label_card_on_door')
                            @else
                                NA
                            @endif
                            </td>
                            <td>
							@if($row->payment_status == 'P')
                                @lang('custom_admin.lab_order_payment_pending')
							@elseif($row->payment_status == 'C')
								@lang('custom_admin.lab_order_payment_completed')
							@else
                                NA
							@endif
                            </td>    
                            <td>
                {{date('d.m.Y', strtotime($row->purchase_date))." ".date('H:i', strtotime($row->purchase_date))}}
            </td>                
                            <td>
							@if ($row->delivery_is_as_soon_as_possible == 'N')
								{{date('d.m.Y', strtotime($row->delivery_date))." ".date('H:i', strtotime($row->delivery_time))}}
							@else
								@lang('custom_admin.label_as_soon_as_possible')
							@endif
							</td>
							<td>
                            @if ($row->delivery_type == 'Delivery')
                                @lang('custom_admin.new_lab_order_delivery_time')
                            @else
                                @lang('custom_admin.new_lab_order_click_collect')
                            @endif
							</td>
                            @php
                            $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice);
                            // Start :: For bottom part calculation
                            if ($row->payment_method == '1' && $row->status != 'C') { // Cash Payment
                                $cashPayment += $orderTotalPrice;
                            } else if ($row->payment_method == '2' && $row->status != 'C') { // Online Payment
                                $onlinePayment += $orderTotalPrice;
                            } else if ($row->payment_method == '3' && $row->status != 'C') { // Card Payment
                                $cardPayment += $orderTotalPrice;
                            } else if ($row->status == 'C') { // Cancelled Payment
                                $cancelledPayment += $orderTotalPrice;
                            }
                            // End :: For bottom part calculation
                            @endphp
							<td>
								@php $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice); @endphp
								CHF {{AdminHelper::formatToTwoDecimalPlaces($orderTotalPrice)}}
							</td>
							@if ($row->status != 'C')
								@php $totalAmountPageWise += $orderTotalPrice; @endphp
							@endif
                            <td>
							@if ($row->status == 'P' && $row->is_print == '1')
								<a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_sure_to_change_status_to_delivered')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.order.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                	<span class="label label-warning">@lang('custom_admin.lab_order_delivery_status_processing')</span>
								</a>
							@elseif ($row->status == 'P' && $row->is_print == '0')
                                <span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span>
                            @elseif ($row->status == 'D')
                                <span class="label label-success">@lang('custom_admin.lab_order_status_delivered')</span>
							@elseif ($row->status == 'C')
                                <span class="label label-danger">@lang('custom_admin.label_cancelled')</span>
                            @else
                            	<span class="label label-danger">NA</span>
                            @endif
							</td>
                            <td style="text-align:center">
                                <a href="{{ route('admin.'.\App::getLocale().'.order.invoice', [$row->id]) }}" title="@lang('custom_admin.lab_order_invoice')">
                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                </a>
								&nbsp;
								<a href="{{ route('admin.'.\App::getLocale().'.order.details', [$row->id]) }}" target="_blank">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="8">@lang('custom_admin.lab_no_records_found')</td>
                        </tr>
                      @endif
                    </table>                    
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                @if(count($orders)>0)
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="pull-left page_of_margin">
                        {{ AdminHelper::paginationMessage($orders) }}
                      </div>
                    </div>
                    <div class="col-sm-9">
						<div class="pull-right page_of_margin m-l-20">
							<a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.order.show-all').'?'.http_build_query(['purchase_date' => $purchaseDate, 'searchText' => $searchText, 'payment_method' => $paymentMethod, 'payment_status' => $paymentStatus, 'status' => $status])}}">@lang('custom_admin.label_show_all')</a>
						</div>


                        <div class="no-margin pull-right">                      
                            {{ $orders->appends(request()->input())->links() }}
                        </div>
					</div>
                  </div>
                @endif
				</div>

        <div class="box-footer clearfix">
                @if(count($orders)>0)
                <div class="page_of_margin">
                    <div class="row">
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.label_orders'): {{ count($orders) }}</strong>
                      </div>
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.label_pay_online_print'): {{ AdminHelper::formatToTwoDecimalPlaces($onlinePayment).' CHF' }}</strong>
                      </div>
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.label_need_pay_cash_print'): {{ AdminHelper::formatToTwoDecimalPlaces($cashPayment).' CHF' }}</strong>
                      </div>
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.lab_order_total'): {{ AdminHelper::formatToTwoDecimalPlaces($totalAmountPageWise).' CHF' }}</strong>                        
                      </div>
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.label_card_on_door_print'): {{ AdminHelper::formatToTwoDecimalPlaces($cardPayment).' CHF' }}</strong>
                      </div>
                      <div class="col-sm-4">
                        <strong>@lang('custom_admin.label_cancelled_print'): {{ AdminHelper::formatToTwoDecimalPlaces(Helper::priceRoundOff($cancelledPayment)).' CHF' }}</strong>
                      </div>
                    </div>
                  </div>
                @endif
                </div>
              
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</section>
<!-- /.content -->
@php
	if (in_array(App::getLocale(), Helper::WEBITE_LANGUAGES)) {
		$jsLang = App::getLocale();
        if($jsLang=='de'){
@endphp
            <script src="{{ asset('js/admin/bower_components/moment/locale/de.js') }}"></script>
@php
        }
	}
@endphp
<script type="text/javascript">
$(function () {
  $('.dwnloadbtn').click(function(){
    $('.lab_report_download_text').show();
  });
    moment.lang('de', {
        week: { dow: 1 }
    });
});
</script>
@endsection
