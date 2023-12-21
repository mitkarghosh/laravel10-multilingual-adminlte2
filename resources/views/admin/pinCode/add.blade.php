@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.pinCode.list')}}"><i class="fa fa-map-pin" aria-hidden="true"></i> @lang('custom_admin.lab_pin_code_list')</a></li>
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
                                    'route' => ['admin.'.\App::getLocale().'.pinCode.addsubmit'],
                                    'name'  => 'addPinCodeForm',
                                    'id'    => 'addPinCodeForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_new_pin_code')<span class="red_star">*</span></label>
                                    {{ Form::text('code', null, array(
                                                                'id' => 'code',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_pin_area')<span class="red_star">*</span></label>
                                    {{ Form::text('area', null, array(
                                                                'id' => 'area',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimum_order_amount">@lang('custom_admin.lab_minimum_order_amount')<span class="red_star">*</span></label>
                                    <div class="input-group" id="minimum_order_amount_div">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::text('minimum_order_amount', null, array(
                                                                'id' => 'minimum_order_amount',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_charge">@lang('custom_admin.lab_delivery_charge')</label>
                                    <div class="input-group" id="delivery_charge_div">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::text('delivery_charge', 0.00, array(
                                                                'id' => 'delivery_charge',
                                                                'class' => 'form-control',
                                                                'placeholder' => '', )) }}
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>                        
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.pinCode.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3Senne2jWZxHZZ5VRacxPUtxbiUToGMI&sensor=false&libraries=places"></script>
<script>
   function otherPickupLocation() {
      var inputPickupLocation = document.getElementById('other_pickup_location');
      new google.maps.places.Autocomplete(inputPickupLocation);
   }
   google.maps.event.addDomListener(window, 'load', otherPickupLocation);

   function otherDropLocation() {
      var inputDropLocation = document.getElementById('other_drop_location');
      new google.maps.places.Autocomplete(inputDropLocation);
   }
   google.maps.event.addDomListener(window, 'load', otherDropLocation);
   
</script>

@endsection