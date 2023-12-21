@extends('admin.layouts.app', ['title' => $panel_title])

 @section('content')

 <section class="content-header">
    <h1>
		{{ $page_title }}
    </h1>
	<ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $page_title }}</li>
	</ol>
</section>

  
<section class="content">
    <div class="row">
      	<div class="col-xs-12">
        	<div class="box">
          		<div class="box-header">
            		<div class="box-tools">
              			<div class="input-group input-group-sm" style="width: 150px;">
                		<!-- <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">
                		<div class="input-group-btn">
                  			<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                		</div>-->
              			</div>
            		</div>
          		</div>
          		<!-- /.box-header -->

          		@include('admin.elements.notification')
          
          		<div class="box-body table-responsive ">
            		<table class="table table-hover table-bordered">
              			<tr>
                			<th>@lang('custom_admin.lab_role_name')</th>
                			<th>@lang('custom_admin.lab_action')</th>
              			</tr>
				@if (count($list) > 0)
					@foreach ($list as $row)
              			<tr>
                			<td>{{ $row->name}}</td>
                			<td>
                  				<a href="{{ route('admin.'.\App::getLocale().'.role.edit', $row->id) }}" class="btn btn-info btn-sm" role="button">
								  	<i class="fa fa-pencil" aria-hidden="true"></i>
								</a>
								&nbsp;
                  				<a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="@lang('custom_admin.lab_delete')" data-href="{{ route('admin.'.\App::getLocale().'.role.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            
				<div class="box-footer clearfix">
				@if (count($list) > 0)
					<div class="row">
						<div class="col-sm-3">
							<div class="pull-left page_of_margin">
								{{ AdminHelper::paginationMessage($list) }}
							</div>
						</div>
						<div class="col-sm-9">
							<div class="pull-right page_of_margin m-l-20">
							  <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.role.show-all')}}">@lang('custom_admin.label_show_all')</a>
							</div>
							<div class="no-margin pull-right">                      
								{{ $list->appends(request()->input())->links() }}
							</div>
						</div>
					</div>
				@endif
				</div>            
          	<!-- /.box-body -->
        	</div>
        	<!-- /.box -->
      	</div>
    </div>      
</section>

@endsection