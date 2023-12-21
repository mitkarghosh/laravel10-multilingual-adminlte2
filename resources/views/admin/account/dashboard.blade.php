@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('custom_admin.lab_dashboard') of <strong> {{ Helper::getAppName() }} </strong></h1>
  <ol class="breadcrumb">
      <li><a><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
      <li class="active">@lang('custom_admin.lab_dashboard')</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="box-header">
                    @include('admin.elements.notification')
                </div>
                <div class="box-body">
                    <div class="row">
                        {{-- user start --}}
                        <div class="col-lg-6 col-xs-6">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{ $totalUser }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_active_users')</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{ route('admin.'.\App::getLocale().'.user.list') }}" class="small-box-footer">
                                    @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        {{-- user start --}}

                        {{-- product start --}}
                        <div class="col-lg-6 col-xs-6">                        
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{ $totalProducts }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_active_products')</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-gift"></i>
                                </div>
                                <a href="{{ route('admin.'.\App::getLocale().'.product.list') }}" class="small-box-footer">
                                    @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        {{-- product end --}}
                    </div>

                    <div class="row">&nbsp;</div>

                    <div class="row">
                        {{--  order start --}}
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>{{ $totalOrders }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_orders')</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <a href="{{ route('admin.'.\App::getLocale().'.order.list') }}" class="small-box-footer">
                                    @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        {{-- order end --}}

                        {{--  order new start --}}
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{ $totalNewOrders }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_orders_new')</p>
                                </div>
                            <div class="icon">
                                <i class="fa fa-cutlery"></i>
                            </div>
                            <a href="{{route('admin.'.\App::getLocale().'.order.list')}}?purchase_date=&searchText=&status%5B%5D=P-0" class="small-box-footer">
                                 @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                        </div>
                        {{-- order new end --}}

                        {{--  order processing start --}}
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{ $totalOrdersProcessing }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_orders_processing')</p>
                                </div>
                            <div class="icon">
                                <i class="fa fa-cutlery"></i>
                            </div>
                            <a href="{{route('admin.'.\App::getLocale().'.order.list')}}?purchase_date=&searchText=&status%5B%5D=P-1" class="small-box-footer">
                                 @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                        </div>
                        {{-- order processing end --}}
                        
                        {{--  order delivered start --}}
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>{{ $totalOrdersDelivered }}</h3>
                                    <p>@lang('custom_admin.dashboard_total_orders_delivered')</p>
                                </div>
                            <div class="icon">
                                <i class="fa fa-truck"></i>
                            </div>
                            <a href="{{route('admin.'.\App::getLocale().'.order.list')}}?purchase_date=&searchText=&status%5B%5D=D-1" class="small-box-footer">
                                 @lang('custom_admin.more_info') <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                        </div>
                        {{-- order delivered end --}}
                        
                    </div>

                    <div class="row">&nbsp;</div>

                    <div class="row">
                        {{-- Drinks --}}
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-green">
                                <a href="{{route('admin.'.\App::getLocale().'.drink.list')}}" title="" style="color: #fff;">
                                    <span class="info-box-icon"><i class="fa fa-glass"></i></span>
                                </a>
                                <div class="info-box-content">
                                    <span class="">@lang('custom_admin.dashboard_total_active_drinks')</span>
                                    <span class="info-box-number">{{ $toatlActiveDrinks }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @lang('custom_admin.dashboard_total_inactive_drinks'): <strong>{{$toatlInactiveDrinks}}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Drinks --}}

                        {{-- Special Menu --}}
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-yellow">
                                <a href="{{route('admin.'.\App::getLocale().'.specialMenu.list')}}" title="" style="color: #fff;">
                                    <span class="info-box-icon"><i class="fa fa-heart"></i></span>
                                </a>
                                <div class="info-box-content">
                                    <span class="">@lang('custom_admin.dashboard_total_active_special')</span>
                                    <span class="info-box-number">{{ $toatlActiveSpecials }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @lang('custom_admin.dashboard_total_inactive_special'): <strong>{{$toatlInactiveSpecials}}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Special Menu --}}

                        {{-- Category --}}
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-red">
                                <a href="{{route('admin.'.\App::getLocale().'.category.list')}}" title="" style="color: #fff;">
                                    <span class="info-box-icon"><i class="fa fa-book"></i></span>
                                </a>
                                <div class="info-box-content">
                                    <span class="">@lang('custom_admin.dashboard_total_active_category')</span>
                                    <span class="info-box-number">{{ $toatlActiveCategories }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @lang('custom_admin.dashboard_total_inactive_category'): <strong>{{$toatlInactiveCategories}}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Category --}}                        
                        
                        {{-- Tag --}}
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-aqua">
                                <a href="{{route('admin.'.\App::getLocale().'.tag.list')}}" title="" style="color: #fff;">
                                    <span class="info-box-icon"><i class="fa fa-tags"></i></span>
                                </a>
                                <div class="info-box-content">
                                    <span class="">@lang('custom_admin.dashboard_total_active_tag')</span>
                                    <span class="info-box-number">{{ $toatlActiveTags }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @lang('custom_admin.dashboard_total_inactive_tag'): <strong>{{$toatlInactiveTags}}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Tag --}}
                        
                    </div>

                    <div class="row">&nbsp;</div>

                    <div class="row">
                        {{-- Latest Orders --}}
                        <div class="col-md-6">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('custom_admin.dashboard_latest_orders')</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table no-margin">
                                            <thead>
                                                <tr>
                                                    <th>@lang('custom_admin.dashboard_order_id')</th>
                                                    <th>@lang('custom_admin.dashboard_order_on')</th>
                                                    <th>@lang('custom_admin.dashboard_order_delivery_datetime')</th>
                                                    <th>@lang('custom_admin.dashboard_order_status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        @if($newOrdersListing->count() > 0)
                                            @foreach($newOrdersListing as $key => $orderData)
                                                <tr>
                                                    <td><a href="{{ route('admin.'.\App::getLocale().'.order.details', [$orderData->id]) }}">{{ $orderData->unique_order_id }}</a></td>
                                                    <td>{{date('d.m.Y H:i', strtotime($orderData->purchase_date))}}</td>
                                                    <td>
                                                    @if ($orderData->delivery_is_as_soon_as_possible == 'N')
                                                        {{date('d.m.Y', strtotime($orderData->delivery_date)).' '.date('H:i', strtotime($orderData->delivery_time))}}
                                                    @else
                                                        @lang('custom_admin.label_as_soon_as_possible')
                                                    @endif
                                                    </td>
                                                    <td>
                                                        <span class="label label-info">@lang('custom_admin.lab_order_delivery_status_new')</span>                 
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                            
                                <div class="box-footer text-center">
                                    <a href="{{ route('admin.'.\App::getLocale().'.order.list') }}" class="">
                                        @lang('custom_admin.dashboard_order_view')
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- Latest Orders --}}
                        
                        {{-- Processing Orders --}}
                        <div class="col-md-6">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('custom_admin.dashboard_process_orders')</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table no-margin">
                                            <thead>
                                                <tr>
                                                    <th>@lang('custom_admin.dashboard_order_id')</th>
                                                    <th>@lang('custom_admin.dashboard_order_on')</th>
                                                    <th>@lang('custom_admin.dashboard_order_delivery_datetime')</th>
                                                    <th>@lang('custom_admin.dashboard_order_status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        @if($processingOrdersListing->count() > 0)
                                            @foreach($processingOrdersListing as $key => $orderData)
                                                <tr>
                                                    <td><a href="{{ route('admin.'.\App::getLocale().'.order.details', [$orderData->id]) }}">{{ $orderData->unique_order_id }}</a></td>
                                                    <td>{{date('d.m.Y H:i', strtotime($orderData->purchase_date))}}</td>
                                                    <td>
                                                    @if ($orderData->delivery_is_as_soon_as_possible == 'N')
                                                        {{date('d.m.Y', strtotime($orderData->delivery_date)).' '.date('H:i', strtotime($orderData->delivery_time))}}
                                                    @else
                                                        @lang('custom_admin.label_as_soon_as_possible')
                                                    @endif
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">@lang('custom_admin.lab_order_delivery_status_processing')</span>                 
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                            
                                <div class="box-footer text-center">
                                    <a href="{{ route('admin.'.\App::getLocale().'.order.list') }}" class="">
                                        @lang('custom_admin.dashboard_order_view')
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- Processing Orders --}}
                        
                    </div>

                    <div class="row">
                    {{-- New Users --}}
                        <div class="col-md-12">
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('custom_admin.dashboard_new_users')</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                    
                                    </div>
                                </div>
                                <div class="box-body no-padding">
                                @if($newUsers->count()>0)
                                    <ul class="users-list clearfix">
                                    @foreach($newUsers as $key => $user)
                                        @php
                                        $userAvatar = URL:: asset('images/site/sample/avatar5.jpg');
                                        if ($user->avatarDetails != null) {
                                            if (file_exists(public_path('/uploads/avatar/thumbs/'.$user->avatarDetails->image))) {
                                                $userAvatar = URL::to('/').'/uploads/avatar/thumbs/'.$user->avatarDetails->image;
                                            }
                                        }
                                        @endphp
                                        <li>                                        
                                            <img src="{{$userAvatar}}" alt=""><br>
                                            {{ $user->full_name }}
                                            <span class="users-list-date">{{ $user->phone_no }}</span>
                                            {{-- <span class="users-list-date">{{ $user->email }}</span> --}}
                                        </li>
                                    @endforeach
                                    </ul>
                                @endif                                
                                </div>
                                <div class="box-footer text-center">
                                    <a href="{{ route('admin.'.\App::getLocale().'.user.list') }}" class="uppercase">@lang('custom_admin.dashboard_all_users')</a>
                                </div>
                            </div>
                        </div>
                        {{-- New Users --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Info boxes -->
    {{-- show admin dashboard 1st row start --}}
    <div class="row">        
    </div>
    {{-- show admin dashboard 1st row end --}}

</section>
<!-- /.content -->

@endsection


