<h3 class="box-title">@lang('custom_admin.label_processing_order_list'):</h3>
<table class="table table-bordered">
    <tr>
        <th>@lang('custom_admin.lab_order_id')</th>
        <th>@lang('custom_admin.lab_order_customer_name')</th>
        <th>@lang('custom_admin.label_order_delivery_area')</th>
        <th>@lang('custom_admin.lab_payment_method')</th>
        <th>@lang('custom_admin.lab_order_payment_status')</th>        
        <th>@lang('custom_admin.dashboard_order_on')</th>
        <th>@lang('custom_admin.lab_order_delivery_time')</th>
        <th>@lang('custom_admin.label_delivery_type')</th>
        <th>@lang('custom_admin.lab_order_total')</th>
        <th>@lang('custom_admin.dashboard_order_status')</th>        
        <th class="action_width" style="text-align:center">@lang('custom_admin.lab_action')</th>
    </tr>                        
    @if(count($processingOrders) > 0)
        @foreach ($processingOrders as $key => $row)
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
            <td>{{ $row->delivery_post_code.' '.$row->delivery_city }}</td>
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
            {{-- <td>{{date('d/m/Y', strtotime($row->purchase_date))}}</td> --}}
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
            <td>
                @php $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice); @endphp
                CHF {{AdminHelper::formatToTwoDecimalPlaces($orderTotalPrice)}}
            </td>
            <td>
            @if ($row->status == 'P' && $row->is_print == '1')
                <a class="color_white" href="javascript:void(0)" onclick="return processingToDeliveredSweetalertMessageRender(this, '@lang('custom_admin.lab_sure_to_change_status_to_delivered')',  'warning',  true)" data-rowid="{{$row->id}}" title="@lang('custom_admin.lab_status')">
                    <span class="label label-warning">@lang('custom_admin.lab_order_delivery_status_processing')</span>
                </a>
            @elseif ($row->status == 'P' && $row->is_print == '0')
                <span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span>
            @elseif ($row->status == 'D')
                <span class="label label-success">@lang('custom_admin.lab_order_status_delivered')</span>
            @else
                <span class="label label-danger">NA</span>
            @endif
            </td>
            <td style="text-align:center">
                <a href="{{ route('admin.'.\App::getLocale().'.order.invoice-print', [$row->id]) }}" title="@lang('custom_admin.lab_order_invoice')" target="_blank">
                    <i class="fa fa-files-o" aria-hidden="true"></i>
                </a>
                &nbsp;
                <a href="{{ route('admin.'.\App::getLocale().'.order.details', [$row->id]) }}" target="_blank">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
                @if ($row->status != 'D')
                    &nbsp;
                    <a href="javascript:void(0)" onclick="return cancelOrderSweetAlertMessageRender(this, '@lang('custom_admin.error_sure_to_cancel_order')',  'warning',  true)" data-rowid="{{$row->id}}" title="@lang('custom_admin.btn_new_cancel')">
                        <i class="fa fa-ban" aria-hidden="true"></i>
                    </a>
                @endif
            </td>
        </tr>
        @endforeach
    @else
        <tr>
            <td colspan="15">@lang('custom_admin.lab_no_records_found')</td>
        </tr>
    @endif
</table>          
