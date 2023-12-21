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
                @include('admin.elements.notification')

                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('custom_admin.lab_order_id')</th>
                            <th>@lang('custom_admin.lab_order_customer_name')</th>
                            <th>@lang('custom.label_food_quality')</th>
                            <th>{{ ucwords(trans('custom.labe_delivery_time')) }}</th>
                            <th>{{ ucwords(trans('custom.label_driver_friendliness')) }}</th>
							<th>@lang('custom_admin.label_reviews')</th>
                            <th class="action_width" style="text-align:center">@lang('custom_admin.lab_action')</th>
                        </tr>                        
                      @if(count($reviews) > 0)
						@foreach ($reviews as $key => $row)
						<tr>
                            <td>{{ $row->orderDetails->unique_order_id }}</td>
                            <td>{{ $row->userDetails->full_name }}</td>
							<td>{{ $row->food_quality }}</td>
							<td>{{ $row->delivery_time }}</td>
                            <td>{{ $row->driver_friendliness }}</td>
							<td>@if ($row->short_review != null){{ substr($row->short_review,0,10).'...' }} @else NA @endif</td>
							<td class="text_align_center">
                            	<a href="{{ route('admin.'.\App::getLocale().'.review.details', [$row->id]) }}" title="@lang('custom_admin.lab_view')" class="btn btn-info btn-sm">
                              		<i class="fa fa-eye" aria-hidden="true"></i>
                            	</a>
                            	&nbsp;
                            	<a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" data-href="{{ route('admin.'.\App::getLocale().'.review.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>
                          	</td>
                        </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="8">@lang('custom_admin.lab_no_records_found')</td>
                        </tr>
                      @endif
                    </table>                    
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pull-right page_of_margin">
                                <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.review.list')}}">@lang('custom_admin.label_pageview')</a>
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