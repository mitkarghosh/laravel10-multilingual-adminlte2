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
										'route' =>  ['admin.'.\App::getLocale().'.coupon.list'],
										'id' => 'searchCouponForm',
                                        'novalidate' => true)) }}
                          {{ Form::text('searchText', (isset($searchText)) ? $searchText:null, array(
                                        'id' => 'searchText',
                                        'placeholder' => 'Search by coupon code',
                                        'class' => 'form-control pull-right')) }}
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.'.\App::getLocale().'.coupon.list') }}" class="btn btn-default"><i class="fa fa-refresh"></i></a>
                            </div>
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                @include('admin.elements.notification')

                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered">
                        <tr>
                          <th>@lang('custom_admin.lab_coupon_code')</th>
                          <th>@lang('custom_admin.lab_discount_type')</th>
                          <th>@lang('custom_admin.lab_amount')(CHF)</th>
                          <th>@lang('custom_admin.lab_has_minimum_cart_amount')</th>
                          <th>@lang('custom_admin.label_is_one_time_use')</th>
                          <th>@lang('custom_admin.label_is_one_time_per_user')</th>
                          <th>@lang('custom_admin.lab_duration')</th>
                          <th>@lang('custom_admin.lab_status')</th>
                          	<th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                        </tr>
                      @if(count($allCoupon) > 0)
                        @foreach ($allCoupon as $row)
                        <tr>
                            <td>{{ $row['code'] }}</td>
                            <td>@if($row['discount_type'] == 'F')@lang('custom_admin.lab_flat') @else @lang('custom_admin.lab_percent') @endif</td>
							<td>{{ $row['amount'] }}</td>
							<td>@if($row['has_minimum_cart_amount'] == 'Y')@lang('custom_admin.lab_yes') @else @lang('custom_admin.lab_no') @endif</td>
							<td>@if($row['is_one_time_use'] == 'Y')@lang('custom_admin.lab_yes') @else @lang('custom_admin.lab_no') @endif</td>
              <td>  @if($row->is_one_time_use_per_user == 'Y')@lang('custom_admin.lab_yes') @else @lang('custom_admin.lab_no') @endif</td>
                            <td>
								{{ date('d.m.Y H:i', $row->start_time).' - ' }}
								@if ($row->end_time != null) {{ date('d.m.Y H:i', $row->end_time) }} @else NA @endif
							</td>
                            <td>
                              <span class="label @if($row->status == 1) label-success @else label-danger @endif">
								@if($row['status'] == '1')
								<a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.coupon.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
									@lang('custom_admin.lab_active')
								</a>
							@else
							<a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.coupon.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
								@lang('custom_admin.lab_inactive')
							</a>
                            @endif
                              </span>
                            </td>
                            <td style="text-align:center">
								{{-- @if($row->status == 1 && ($row->end_time != null && $row->end_time >= strtotime(now()))) --}}							
								<a href="{{ route('admin.'.\App::getLocale().'.coupon.edit', [$row->id]) }}" title="@lang('custom_admin.lab_edit')" class="btn btn-info btn-sm">
									<i class="fa fa-pencil" aria-hidden="true"></i>
							  	</a>
							  &nbsp;
							  <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="@lang('custom_admin.lab_delete')" data-href="{{ route('admin.'.\App::getLocale().'.coupon.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
                @if(count($allCoupon)>0)
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="pull-left page_of_margin">
                        {{ AdminHelper::paginationMessage($allCoupon) }}
                      </div>
                    </div>
                    <div class="col-sm-9">
                      <div class="pull-right page_of_margin m-l-20">
                        <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.coupon.show-all')}}">@lang('custom_admin.label_show_all')</a>
                      </div>
                      <div class="no-margin pull-right">                      
                        {{ $allCoupon->appends(request()->input())->links() }}
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