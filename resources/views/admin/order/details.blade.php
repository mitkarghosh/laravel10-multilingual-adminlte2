@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

@php $orderTotalPrice = $getOrderDetails['total_price']; @endphp

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $page_title }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.order.list')}}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> @lang('custom_admin.lab_order_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                @include('admin.elements.notification')

                <div class="box-body">
                    <div class="box-body">                        
                        <div class="row">
                            <div class="col-md-3">
                                <label> @lang('custom_admin.lab_order_ordered_on')</label>: {{date('d.m.Y H:i', strtotime($orderDetails['purchase_date']))}}
                            </div>
                            <div class="col-md-3">
                                <label>@lang('custom_admin.lab_order_id')</label>: {{$orderDetails['unique_order_id']}}<br>
                            </div>
                            <div class="col-md-3">
                                <label>@lang('custom_admin.lab_payment_method')</label>: 
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
                            </div>
                        @if ($orderDetails['order_status'] == 'O')
                            <div class="col-md-3">
                                <label>@lang('custom_admin.lab_order_delivery_status')</label>:
                                @if ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '0')
                                    <span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span>
                                @elseif ($orderDetails['status'] == 'P' && $orderDetails['is_print'] == '1')
                                    <span class="label label-warning">@lang('custom_admin.lab_order_delivery_status_processing')</span>
                                @elseif ($orderDetails['status'] == 'D')
                                    <span class="label label-success">@lang('custom_admin.lab_order_delivery_status_delivered')</span>
                                @elseif ($orderDetails['status'] == 'C')
                                    <span class="label label-danger">@lang('custom_admin.label_cancelled')</span>
                                @else
                                    <span class="label label-danger">NA</span>
                                @endif
                            </div>
                        @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label> @lang('custom_admin.label_delivery_type')</label>: 
                                @if ($orderDetails['delivery_type'] == 'Delivery')
                                    @lang('custom_admin.new_lab_order_delivery_time')
                                @else
                                    @lang('custom_admin.new_lab_order_click_collect')
                                @endif
                            </div>
                        </div>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label>@lang('custom_admin.lab_order_customer_details')</label>
                                <p>{{$orderDetails['delivery_full_name']}}</p>
                                <p>{{$orderDetails['delivery_phone_no']}}</p>
                                <p>{{$orderDetails['delivery_email']}}</p>
                            </div>
                            <div class="col-md-6">
                                <label>@lang('custom_admin.lab_order_delivery_address')</label>
                                @if ($orderDetails['delivery_type'] == 'Delivery')  
                                <p>
                                    @if ($orderDetails['delivery_company'] != null)
                                        {!!$orderDetails['delivery_company'].'<br>'!!}
                                    @endif
                                    @if ($orderDetails['delivery_door_code'] != null)
                                        {{$orderDetails['delivery_door_code'].', '}}
                                    @endif
                                    {{$orderDetails['delivery_street']}}
                                    @if ($orderDetails['delivery_floor'] != null)
                                        {{', '.$orderDetails['delivery_floor']}}
                                    @endif
                                    <br/>
                                    {{$orderDetails['delivery_post_code'].' '.$orderDetails['delivery_city']}}
                                </p>
                                @else
                                    <p>@lang('custom_admin.new_lab_order_click_collect')</p>
                                @endif
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <label style="padding-left: 15px;">@lang('custom_admin.lab_order_product_details')</label>
                        @foreach($getOrderDetails['product_details'] as $key => $val)
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <p>
                                        {{$val['quantity']}}x {{$val['title']}} @if($val['attribute'] != '') {{' - '.$val['attribute']}} @endif
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
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p>CHF {{$val['total_price']}}</p>
                                </div>
                            </div>                            
                        @endforeach                            
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <span><strong>@lang('custom.label_subtotal')</strong></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <span>CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</span>
                                </p>
                            </div>
                        </div>

                        @if ($orderDetails['delivery_type'] == 'Delivery' && $orderDetails['delivery_charge'] > 0)
                            @php $orderTotalPrice += $orderDetails['delivery_charge']; @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <span><strong>@lang('custom_admin.lab_delivery_charge')</strong></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <span>CHF {{Helper::formatToTwoDecimalPlaces($orderDetails['delivery_charge'])}}</span>
                                </p>
                            </div>
                        </div>
                        @endif

                        @php
                        if ($orderDetails['payment_method'] == '2') {
                            $orderTotalPrice += $orderDetails['card_payment_amount'];
                        @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <span><strong>@lang('custom.label_new_card_payment')</strong></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <span>CHF {{Helper::formatToTwoDecimalPlaces(Helper::priceRoundOff($orderDetails['card_payment_amount']))}}</span>
                                    </p>
                                </div>
                            </div>
                        @php
                        }
                        @endphp

                        @if ($orderDetails['coupon_code'] != null && $orderDetails['discount_amount'] > 0)
                            @php $orderTotalPrice = $orderTotalPrice - $orderDetails['discount_amount']; @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <span><strong>@lang('custom_admin.lab_discount')</strong> ({!! $orderDetails['coupon_code'] !!})</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <span>CHF -{{Helper::formatToTwoDecimalPlaces($orderDetails['discount_amount'])}}</span>
                                </p>
                            </div>
                        </div>
                        @endif

                        @php $orderTotalPrice = Helper::priceRoundOff($orderTotalPrice); @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <span><strong>@lang('custom_admin.lab_order_total')</strong></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <span><strong>CHF {{Helper::formatToTwoDecimalPlaces($orderTotalPrice)}}</strong></span>
                                </p>
                            </div>
                        </div>

                        <hr>
                        @if ($orderDetails['delivery_note'])
                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    <span><strong>@lang('custom_admin.lab_delivery_note'):</strong> {{$orderDetails['delivery_note']}}</span>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</section>
<!-- /.content -->

@endsection