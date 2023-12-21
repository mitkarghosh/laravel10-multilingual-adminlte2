@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $data['page_title'] }}</li>
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
                                    'route' => ['admin.'.\App::getLocale().'.specialHour.editsubmit', $data['id']],
                                    'name'  => 'editSpecialHourForm',
                                    'id'    => 'editSpecialHourForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row addField_0">
                            <div class="">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="CouponDuration">@lang('custom_admin.lab_date')<span class="red_star">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            {{ Form::text('special_date', date('d.m.Y',strtotime($details->special_date)), array(
                                                                            'id' => 'special_date',
                                                                            'class' => 'form-control',
                                                                            'autocomplete' => 'off',
                                                                            'required' => 'required' )) }}
                                        </div>                        
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="LastName">@lang('custom_admin.lab_delivery_holiday')</label>
                                        <div><input type="checkbox" name='delivery[holiday][]' class="isClosed" value="1" @if ($details->holiday == 1)checked @endif></div>
                                    </div>
                                </div>
                                @php
                                $startTime = date('H:i');
                                if ($details->start_time != null) {
                                    $startTime = date('H:i', strtotime($details->start_time));
                                }
                                $endTime = date('H:i');
                                if ($details->end_time != null) {
                                    $endTime = date('H:i', strtotime($details->end_time));
                                }
                                @endphp
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="LastName">@lang('custom_admin.lab_delivery_start_time')<span class="red_star">*</span></label>
                                        <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                            <span class="input-group-addon validTime @if ($details->holiday == 1)pointerEventsNone @endif">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                            <input type="text" name='delivery[slot][0][start_time][]' id="start_time_0" readonly class="form-control validTime @if ($details->holiday == 1)pointerEventsNone @endif" value="{{$startTime}}">
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="LastName">@lang('custom_admin.lab_delivery_end_time')<span class="red_star">*</span></label>
                                        <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                            <span class="input-group-addon validTime @if ($details->holiday == 1)pointerEventsNone @endif">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                            <input type="text" name='delivery[slot][0][end_time][]' id="close_time_0" readonly class="form-control validTime @if ($details->holiday == 1)pointerEventsNone @endif" value="{{$endTime}}">
                                        </div>
                                    </div>
                                </div>
                                @if ($details->start_time2 == null && $details->end_time2 == null)
                                <div class="col-md-1 addMoreRows_0">
                                    <label for="title">&nbsp;</label><br />
                                    <button class="btn btn-success add-more @if ($details->holiday == 1)pointerEventsNone @endif" data-attrkey="0" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                                @else
                                <div class="col-md-1 addMoreRows_0"></div>
                                @endif
                            </div>
                            @if ($details->start_time2 != null && $details->end_time2 != null)
                            <div class="new_row_0">
                                <div class="col-md-3">
                                    <div class="form-group">&nbsp;</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">&nbsp;</div>
                                </div>
                                @php
                                $startTime2 = date('00:00');
                                if ($details->start_time2 != null) {
                                    $startTime2 = date('H:i', strtotime($details->start_time2));
                                }
                                $endTime2 = date('23:59');
                                if ($details->end_time2 != null) {
                                    $endTime2 = date('H:i', strtotime($details->end_time2));
                                }
                                @endphp
                                <div class="col-md-3">
                                    <div class="form-group">                                    
                                        <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                            <span class="input-group-addon validTime @if ($details->holiday == 1)pointerEventsNone @endif">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                            <input type="text" name='delivery[slot][0][start_time][]' id="start_time_1" readonly class="form-control validTime @if ($details->holiday == 1)pointerEventsNone @endif" value="{{$startTime2}}">
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true">
                                            <span class="input-group-addon validTime @if ($details->holiday == 1)pointerEventsNone @endif">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                            <input type="text" name='delivery[slot][0][end_time][]' id="close_time_1" readonly class="form-control validTime @if ($details->holiday == 1)pointerEventsNone @endif" value="{{$endTime2}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 delRows_0">
                                    <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" data-href="{{ route('admin.'.\App::getLocale().'.specialHour.slot-delete', [$details->id]) }}" class="deleteRow btn btn-danger del_current_row"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        @endif
                        </div>
                    
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_update')</button>
                                <a href="{{ route('admin.'.\App::getLocale().'.specialHour.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
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
    var currentTime     = '{{date("00:00")}}';
    var currentTimeEnd  = '{{date("23:59")}}';

    // Add more section start //
    $(document).on('click', '.add-more', function() {
        var keyAttr = $(this).data('attrkey');        
        var cols = '';
        var newRow = $('<div class="new_row_'+keyAttr+'" style="margin-top: 10px;">');
        cols += '<div class="col-md-3"><div class="form-group"><label for="FirstName">&nbsp;</label></div></div>';
        cols += '<div class="col-md-2"><div class="form-group"><label for="LastName">&nbsp;</label><div></div></div></div>';
        cols += '<div class="col-md-3"><div class="form-group"><div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true"><span class="input-group-addon validTime"><span class="glyphicon glyphicon-time"></span></span><input type="text" name="delivery[slot]['+keyAttr+'][start_time][]" id="start_time_1" readonly class="form-control" value="'+currentTime+'"></div></div></div>';
        cols += '<div class="col-md-3"><div class="form-group"><div class="input-group clockpicker" data-placement="left" data-align="top" data-autoclose="true"><span class="input-group-addon validTime"><span class="glyphicon glyphicon-time"></span></span><input type="text" name="delivery[slot]['+keyAttr+'][end_time][]" id="close_time_1" readonly class="form-control" value="'+currentTimeEnd+'"></div></div></div>';
        cols += '<div class="col-sm-1"><a class="deleteRow btn btn-danger ibtnDel" data-attrkey="'+keyAttr+'" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
        cols += '</div>';

        newRow.append(cols);
        $(".addField_"+keyAttr).append(newRow);

        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            donetext: 'Done'
        });

        $(this).remove();

    });
    $(".row").on("click", ".ibtnDel", function (event) {
        var keyAttr = $(this).data('attrkey');
        $('.new_row_'+keyAttr).remove();
        $('.addMoreRows_'+keyAttr).append('<button class="btn btn-success add-more" id="addrow" data-attrkey="'+keyAttr+'" type="button"><i class="fa fa-plus"></i></button>');
    });
    // Add more section end //

    /* Restriction on key & right click */
    $('#special_date').keydown(function(e){
        var keyCode = e.which;
        if ( (keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 8 || keyCode === 122 || keyCode === 32 || keyCode == 46 ) {
            e.preventDefault();
        }
    });
    $("#special_date").on("contextmenu",function(){
       return false;
    });
    /* Restriction on key & right click */

    $('#special_date').datetimepicker({
        useCurrent: false,
        format: 'DD.MM.YYYY',
        // format: 'YYYY-MM-DD',
        // minDate: moment()
    });

    // is closed checked then disabled date
    $('.isClosed').click(function() {
        if ($(this).is(':checked')) {
            $('.validTime').addClass('pointerEventsNone');
            $('.addrow').addClass('pointerEventsNone');
            $('.new_row_0').remove();
            $('.addMoreRows_0').html('<label for="title">&nbsp;</label><br><button class="btn btn-success add-more pointerEventsNone" id="addrow" data-attrkey="0" type="button"><i class="fa fa-plus"></i></button>');
        } else {
            $('.validTime').removeClass('pointerEventsNone');
            $('.addrow').removeClass('pointerEventsNone');
            $('.addMoreRows_0').html('<label for="title">&nbsp;</label><br><button class="btn btn-success add-more" id="addrow" data-attrkey="0" type="button"><i class="fa fa-plus"></i></button>');
        }
    });

});
</script>

@endsection