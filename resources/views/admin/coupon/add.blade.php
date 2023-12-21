@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')
    
@php
    $oldDuration = explode(" - ", old('coupon_duration'));
@endphp
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.coupon.list')}}"><i class="fa fa-question-circle" aria-hidden="true"></i> @lang('custom_admin.lab_coupon_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                @include('admin.elements.notification')

                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.coupon.addsubmit'],
                                    'name'  => 'addCouponForm',
                                    'id'    => 'addCouponForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="CouponCode">@lang('custom_admin.lab_coupon_code')<span class="red_star">*</span></label>
                                    {{ Form::text('code', null, array(
                                                                'id' => 'code',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                )) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.label_is_one_time_use')</label>
                                    <div>
                                        <label class="form-check-label cursor_pointer">
                                            {!! Form::checkbox('is_one_time_use', 'Y', null, array('id' => 'is_one_time_use', 'class'=>'form-check-input', 'autocomplete' => 'off')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.label_is_one_time_per_user')</label>
                                    <div>
                                        <label class="form-check-label cursor_pointer">
                                            {!! Form::checkbox('is_one_time_use_per_user', 'Y', null, array('id' => 'is_one_time_use_per_user', 'class'=>'form-check-input', 'autocomplete' => 'off')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="DiscountType">@lang('custom_admin.lab_has_minimum_cart_amount')<span class="red_star">*</span></label>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            {{ Form::select('has_minimum_cart_amount', ['N'=>trans('custom_admin.lab_no'),'Y'=>trans('custom_admin.lab_yes')], (old('has_minimum_cart_amount'))?old('has_minimum_cart_amount'):null, array(
                                                'id' => 'has_minimum_cart_amount',
                                                'class' => 'form-control' )) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="cart-amount-div" style="display: none;">                                    
                                    <label for="Amount">@lang('custom_admin.lab_amount')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::text('cart_amount', null, array(
                                                                        'id' => 'cart_amount',
                                                                        'min' => 0,
                                                                        'placeholder' => '',
                                                                        'class' => 'form-control',
                                                                        )) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="DiscountType">@lang('custom_admin.lab_discount_type')<span class="red_star">*</span></label>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            {{ Form::select('discount_type', ['F'=>trans('custom_admin.lab_flat'),'P'=>trans('custom_admin.lab_percent')], (old('discount_type'))?old('discount_type'):null, array(
                                                        'id' => 'discount_type',
                                                        'class' => 'form-control' )) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">                                    
                                    <label for="Amount">@lang('custom_admin.lab_amount')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon discount_type_lable_change">
                                            CHF
                                        </div>
                                        {{ Form::text('amount', null, array(
                                                                        'id' => 'amount',
                                                                        'min' => 0,
                                                                        'placeholder' => '',
                                                                        'class' => 'form-control',
                                                                        'required' => 'required'
                                                                        )) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="CouponDuration">@lang('custom_admin.lab_start_date_time')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        {{ Form::text('start_time', null, array(
                                                                        'id' => 'start_time',
                                                                        'class' => 'form-control',
                                                                        'autocomplete' => 'off',
                                                                        'required' => 'required' )) }}
                                    </div>                        
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="CouponDuration">@lang('custom_admin.lab_end_date_time')</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        {{ Form::text('end_time', null, array(
                                                                        'id' => 'end_time',
                                                                        'class' => 'form-control',
                                                                        'autocomplete' => 'off' )) }}
                                    </div>                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                                <a href="{{ route('admin.'.\App::getLocale().'.coupon.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script src="{{ asset('js/admin/bower_components/bootstrap-datetimepicker/jquery-2.1.1.min.js') }}"></script>
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
    moment.lang('en', {
        week: { dow: 1 }
    });
});
</script>
<script>
$(function () {
    $('#has_minimum_cart_amount').on('change', function() {
        if ($(this).val() == 'Y') {
            $('#cart-amount-div').show(500);
            $('#cart_amount').val('0');
            $('#cart_amount').attr('required', true);
        } else {
            $('#cart-amount-div').hide(500);
            $('#cart_amount').val('');
            $('#cart_amount').attr('required', false);
        }
    });

    /* Restriction on key & right click */
    $('#start_time,#end_time').keydown(function(e){
        var keyCode = e.which;
        if ( (keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 8 || keyCode === 122 || keyCode === 32 || keyCode == 46 ) {
            e.preventDefault();
        }
    });
    $("#start_time,#end_time").on("contextmenu",function(){
       return false;
    });
    /* Restriction on key & right click */

    $('#start_time').datetimepicker({
        useCurrent: false,
        format: 'DD.MM.YYYY HH:mm',
        // format: 'YYYY-MM-DD HH:mm',
        // minDate: moment()
    });
    $('#end_time').datetimepicker({
        useCurrent: false,
        format: 'DD.MM.YYYY HH:mm',
        // format: 'YYYY-MM-DD 23:59',
    });
    $('#start_time').datetimepicker().on('dp.change', function (e) {
        var incrementDay = moment(new Date(e.date));
        incrementDay.add(0, 'days');
        $('#end_time').data('DateTimePicker').minDate(incrementDay);
        $(this).data("DateTimePicker").hide();
    });

    $('#end_time').datetimepicker().on('dp.change', function (e) {
        var decrementDay = moment(new Date(e.date));
        decrementDay.subtract(0, 'days');
        $('#start_time').data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });

    /**
     * One time use for per user
     */
    $(document).on('click','#is_one_time_use_per_user',function(){
          if($(this).prop('checked')==true){
              $('#is_one_time_use').prop('checked',false);
          }
    })

    $(document).on('click','#is_one_time_use',function(){
          if($(this).prop('checked')==true){
              $('#is_one_time_use_per_user').prop('checked',false);
          }
    })
});
</script>
@endsection