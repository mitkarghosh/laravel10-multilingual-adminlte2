@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $page_title }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.review.list')}}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> @lang('custom_admin.lab_review_list')</a></li>
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
                            <div class="col-md-12">
                                <label> @lang('custom_admin.lab_review_on')</label>: {{date('d.m.Y H:i', strtotime($reviewDetails['created_at']))}}
                            </div>
                            <div class="col-md-12">
                                <label>@lang('custom_admin.lab_order_id')</label>: {{$reviewDetails->orderDetails->unique_order_id}}
                            </div>
                            <div class="col-md-12">
                                <label>@lang('custom.label_food_quality')</label>: {{$reviewDetails->food_quality}}
                            </div>
                            <div class="col-md-12">
                                <label>{{ ucwords(trans('custom.labe_delivery_time')) }}</label>: {{$reviewDetails->delivery_time}}
                            </div>
                            <div class="col-md-12">
                                <label>{{ ucwords(trans('custom.label_driver_friendliness')) }}</label>: {{$reviewDetails->driver_friendliness}}
                            </div>
                            <div class="col-md-12">
                                <label>{{ ucwords(trans('custom_admin.label_reviews')) }}</label>: {{$reviewDetails->short_review}}
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-md-6">
                        <a href="{{ route('admin.'.\App::getLocale().'.review.list').'?page='.$pageNo }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</section>
<!-- /.content -->

@endsection