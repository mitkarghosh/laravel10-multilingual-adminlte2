<h3 class="box-title">@lang('custom_admin.label_new_order_list'):</h3>
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
            <td> @if ($row->delivery_type == 'Delivery') {{ $row->delivery_post_code.' '.$row->delivery_city }} @else @lang('custom_admin.new_lab_order_click_collect') @endif</td>
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
            <td>
                @php $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice); @endphp
                CHF {{AdminHelper::formatToTwoDecimalPlaces($orderTotalPrice)}}
            </td>
            <td>
            @if ($row->status == 'P' && $row->is_print == '1')
                <a class="color_white" href="javascript:void(0)" title="@lang('custom_admin.lab_status')">
                    <span class="label label-warning">@lang('custom_admin.lab_order_delivery_status_processing')</span>
                </a>
            @elseif ($row->status == 'P' && $row->is_print == '0')
                @if ($row->delivery_is_as_soon_as_possible == 'Y')
                <a href="javascript: void(0);" class="deliveryIn" data-ordid="{{$row['id']}}"><span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span></a>
                @else
                <a><span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span></a>
                @endif
            @elseif ($row->status == 'D')
                <span class="label label-success">@lang('custom_admin.lab_order_status_delivered')</span>
            @else
                <span class="label label-danger">NA</span>
            @endif
            </td>
            <td style="text-align:center">
                <a onclick="generateList();" href="{{ route('admin.'.\App::getLocale().'.order.invoice-print', [$row->id]) }}" title="@lang('custom_admin.lab_order_invoice')" target="_blank">
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

<!-- Modal -->
<div id="deliveryInModal" class="modal fade" role="dialog">
    <div class="modal-dialog">  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('custom_admin.lab_deliveryin')</h4>
            </div>
            <div class="modal-body">
                <form id="deliveryInForm" name="deliveryInForm" autocomplete="off">
                    <input type="hidden" name="order_id" id="order_id" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="CouponCode">@lang('custom_admin.lab_delivery_in')<span class="red_star">*</span></label>
                                <select name="delivery_in" id="delivery_in" class="form-control">
                                    <option value="15">15 @lang('custom_admin.lab_minutes')</option>
                                    <option value="30">30 @lang('custom_admin.lab_minutes')</option>
                                    <option value="45">45 @lang('custom_admin.lab_minutes')</option>
                                    <option value="60">60 @lang('custom_admin.lab_minutes')</option>
                                    <option value="75">75 @lang('custom_admin.lab_minutes')</option>
                                    <option value="90">90 @lang('custom_admin.lab_minutes')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary submitClass">@lang('custom_admin.btn_submit_popup')</button>
                        </div>
                        <div class="col-md-6">
                            &nbsp;
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <p id="notify_msg"></p>
            </div>
        </div>  
    </div>
</div>

<script type="text/javascript">
var ajxChk = false;
$(document).on('click', '.deliveryIn', function() {
    $('#notify_msg').html('');
    $('#deliveryInModal').modal('show');
    clearTimeout(timeoutVar);
    $('#order_id').val($(this).data('ordid'));
    $('.submitClass').prop("disabled", false);
});

$(document).on('click', '.submitClass', function() {
    $('.submitClass').prop("disabled", true);
    var submitUrl = $("#website_admin_link").val() + '/securepanel/' + $('#website_lang').val() + '/orders/delivery-in';
    if (ajxChk) {
        return;
    }

    ajxChk = true;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: submitUrl,
        method: 'POST',
        data: {
            order_id: $('#order_id').val(),
            delivery_in: $('#delivery_in').val(),
        },
        success: function (response) {
            ajxChk = false;
            var responseData = jQuery.parseJSON(response);
            if (responseData.type == 'success') {
                window.open($("#website_admin_link").val() + '/securepanel/' + $('#website_lang').val() + '/orders/invoice-print/'+$('#order_id').val(), '_blank');
                $('#notify_msg').html('<span style="color: green;">'+responseData.message+'</span>');
                $('#deliveryInModal').modal('hide');
            } else {
                $('#notify_msg').html('<span style="color: red;">'+responseData.message+'</span>');
                setTimeout(function() {
                    $('#deliveryInModal').modal('hide');
                }, 2000);
            }
            // Get data
            getList();
        }
    });
});
</script>
