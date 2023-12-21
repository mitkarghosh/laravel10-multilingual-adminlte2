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
                        
                        </div>
                    </div>
                </div>

                @include('admin.elements.notification')

                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('custom_admin.lab_date')</th>
                            <th>@lang('custom_admin.lab_delivery_holiday')</th>
							<th>@lang('custom_admin.lab_delivery_start_time') 1</th>
							<th>@lang('custom_admin.lab_delivery_end_time') 1</th>
							<th>@lang('custom_admin.lab_delivery_start_time') 2</th>
							<th>@lang('custom_admin.lab_delivery_end_time') 2</th>
                            <th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                        </tr>
                      @if(count($allSpecialHour) > 0)
                        @foreach ($allSpecialHour as $row)
                        <tr>
                            <td>{{ date('d.m.Y', strtotime($row->special_date)) }}</td>
                            @if ($row->holiday == '0')
                            <td>@lang('custom_admin.lab_no')</td>
                            <td>{{ date('H:i', strtotime($row->start_time)) }}</td>
                            <td>{{ date('H:i', strtotime($row->end_time)) }}</td>
                            <td>@if ($row->start_time2 != null){{ date('H:i', strtotime($row->start_time2)) }} @else N/A @endif</td>
                            <td>@if ($row->end_time2 != null){{ date('H:i', strtotime($row->end_time2)) }} @else N/A @endif</td>
                            @else
                            <td>@lang('custom_admin.lab_yes')</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            @endif
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
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pull-right page_of_margin">
                                <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.specialHour.list')}}">@lang('custom_admin.label_pageview')</a>
                            </div>
                        </div>
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