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
                
				<div class="box-header with-border">
                    <!-- Start: Filter Section -->
					@php
					$searchText = (isset($data['searchText'])) ? $data['searchText']:'';
                    $requestParameters = app('request')->request->all();
                    @endphp
                    <div class="box-header">
                    {{ Form::open(array(
                                      'method' => 'GET',
                                      'class' => '',
                                      'route' =>  ['admin.'.\App::getLocale().'.product.show-all'],
                    					'id' => 'searchproductForm',
                                      'id' => '',
                                      'novalidate' => true)) }}                    
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="searchText">@lang('custom_admin.lab_order_search'):</label>
							  {{ Form::text('searchText', (isset($searchText)) ? $searchText:null, array(
																									'id' => 'searchText',
																									'placeholder' => trans('custom_admin.lab_search_by_title'),
																									'class' => 'form-control pull-right')) }}
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="box-tools">
                              <div class="form-group">
                                <label for="category">@lang('custom_admin.lab_category'):</label>
                                <select name="category[]" id="category" multiple="multiple" class="form-control select2">
							@if ($categoryList)
								@foreach ($categoryList as $item)
									<option value="{{$item->id}}" {{(isset($category) && in_array($item->id,$category)) ? 'selected' : ''}}>{!! $item->local[0]->local_title !!}</option>
								@endforeach
							@endif
                                </select>
                              </div>
                          </div>
                        </div>                        
                      </div>
                    
                      <div class="row">
                        <!-- Filter section start -->
                        <div class="col-md-6">
                          <button type="submit" class="btn btn-primary">@lang('custom_admin.lab_order_filter')</button>
                          <a href="{{ route('admin.'.\App::getLocale().'.product.show-all') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.lab_order_reset')</a>
                        </div>
                        <!-- Filter section end -->                        
                      </div>
                    {!! Form::close() !!}
                    </div>
                    <!-- End: Filter Section -->                    
                </div>

                @include('admin.elements.notification')

                <div class="box-body table-responsive">
                  <table class="table table-bordered">
                      	<tr>
							<th>@lang('custom_admin.lab_image')</th>
							<th>@lang('custom_admin.lab_title')</th>
							<th>@lang('custom_admin.lab_title_dutch')</th>
							<th>@lang('custom_admin.lab_status')</th>
							<th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                      	</tr>
                    @if(count($list) > 0)
                      @foreach ($list as $row)
                      <tr>
						<td>
							@php
							$imgPath = \URL:: asset('images').'/site/'.Helper::NO_IMAGE;
							if ($row->image != null) {
								if(file_exists(public_path('/uploads/product/thumbs'.'/'.$row->image))) {
									$imgPath = \URL::asset('uploads/product/thumbs').'/'.$row->image;
								} else {
									$imgPath = \URL:: asset('images').'/site/'.Helper::NO_IMAGE;
								}
							}
							@endphp
							<img src="{{ $imgPath }}" alt="" width="50px">
						</td>
						<td>{{ $row->title }}</td>
						<td>{{ $row->local[1]->local_title }}</td>
                          <td>
                            <span class="label @if($row->status == 1) label-success @else label-danger @endif">
                            @if($row['status'] == '1')
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')', 'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.product.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_active')
                                </a>
                            @else
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')', 'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.product.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_inactive')
                                </a>
                            @endif
                            </span>
                          </td>
                          <td class="text_align_center">
                            <a href="{{ route('admin.'.\App::getLocale().'.product.edit', [$row->id]) }}" title="@lang('custom_admin.lab_edit')" class="btn btn-info btn-sm">
                              <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.product.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a> 
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
                                <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.product.list').'?'.http_build_query(['searchText' => $searchText, 'category' => $category])}}">@lang('custom_admin.label_pageview')</a>
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