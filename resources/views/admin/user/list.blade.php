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
                <div class="box-header">
                    <!-- Search section -->
                    <div class="box-tools">
                        <div class="input-group input-group-sm search_width">
                        {{ Form::open(array(
                                        'method' => 'GET',
                                        'class' => 'display_table',
                                        'route' =>  ['admin.'.\App::getLocale().'.user.list'],
                                        'id' => 'searchuserForm',
                                        'novalidate' => true)) }}
                          {{ Form::text('searchText', (isset($searchText)) ? $searchText:null, array(
                                        'id' => 'searchText',
                                        'placeholder' => 'Search by Name or Email',
                                        'class' => 'form-control pull-right')) }}
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.'.\App::getLocale().'.user.list') }}" class="btn btn-default"><i class="fa fa-refresh"></i></a>
                            </div>
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <!-- Search Section End -->

                @include('admin.elements.notification')

                <div class="box-body table-responsive">
                  <table class="table table-bordered">
                      <tr>
                          <th>@lang('custom_admin.label_first_name')</th>
						  <th>@lang('custom_admin.label_last_name')</th>
                          <th>@lang('custom_admin.label_email')</th>
						  <th>@lang('custom_admin.lab_address')</th>
						  <th>@lang('custom_admin.lab_order_contact_number')</th>
                          <th>@lang('custom_admin.lab_status')</th>
                          <th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                      </tr>
                    @if (count($userList) > 0)
                      @foreach ($userList as $row)
					  	@php
						$address = 'NA';
						if ($row->userOrders->count()) {
							$address = $row->userOrders[0]->delivery_street.', '.$row->userOrders[0]->delivery_post_code.' '.$row->userOrders[0]->delivery_city;
						} else if ($row->userDeliveryAddresses->count()) {
							$address = $row->userDeliveryAddresses[0]->street.' '.$row->userDeliveryAddresses[0]->post_code.', '.$row->userDeliveryAddresses[0]->city;
						}
						@endphp
                      <tr>
                          <td>{{ $row['first_name'] }}</td>
						  <td>{{ $row['last_name'] }}</td>
                          <td>{{ $row['email'] }}</td>
						  <td>{{ $address  }}</td>
						  <td>{{ $row['phone_no'] }}</td>
                          <td>
                            <span class="label @if($row->status == 1) label-success @else label-danger @endif">
                              @if($row['status'] == '1')
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.user.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_active')
                                </a>
                            @else
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.user.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_inactive')
                                </a>
                            @endif
                            </span>
                          </td>
                          <td class="text_align_center">
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="" data-href="{{ route('admin.'.\App::getLocale().'.user.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                          </td>                    
                      </tr>
                      @endforeach
                    @else
                      <tr>
                        <td colspan="10">@lang('custom_admin.lab_no_records_found')</td>
                      </tr>
                    @endif
                  </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                @if(count($userList)>0)
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="pull-left page_of_margin">
                        {{ AdminHelper::paginationMessage($userList) }}
                      </div>
                    </div>
                    <div class="col-sm-9">
                      <div class="pull-right page_of_margin m-l-20">
                        <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.user.show-all')}}">@lang('custom_admin.label_show_all')</a>
                      </div>
                      <div class="no-margin pull-right">
                        {{ $userList->appends(request()->input())->links() }}
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

@endsection