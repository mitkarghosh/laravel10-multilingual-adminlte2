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
                    <h3 class="box-title">CMS</h3>
                    <!-- Search section -->
                    <div class="box-tools">
                        <div class="input-group input-group-sm search_width">
                        {{ Form::open(array(
                                        'method' => 'GET',
                                        'class' => 'display_table',
                                        'route' =>  ['admin.'.\App::getLocale().'.CMS.list'],
                                        'id' => '',
                                        'novalidate' => true)) }}
                          {{ Form::text('searchText', (isset($searchText)) ? $searchText:null, array(
                                        'id' => 'searchText',
                                        'placeholder' => trans('custom_admin.lab_search_by_title'),
                                        'class' => 'form-control pull-right')) }}
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.'.\App::getLocale().'.CMS.list') }}" class="btn btn-default"><i class="fa fa-refresh"></i></a>
                            </div>
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                @include('admin.elements.notification')

                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('custom_admin.lab_title')</th>
							<th>@lang('custom_admin.lab_title_dutch')</th>
                            <th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                        </tr>
                      @if(count($listData) > 0)
                        @foreach ($listData as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row->local[1]->title }}</td>
                            <td class="text_align_center">
                              <a href="{{ route('admin.'.\App::getLocale().'.CMS.edit', [$row->id]) }}" title="Edit">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                              </a>
                            </td>                            
                        </tr>
                        @endforeach
                      @else
                        <tr>
                          <td colspan="4">@lang('custom_admin.lab_no_records_found')</td>
                        </tr>
                      @endif
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                @if(count($listData)>0)
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="pull-left page_of_margin">
                        {{ AdminHelper::paginationMessage($listData) }}
                      </div>
                    </div>
                    <div class="col-sm-9">
                      <div class="no-margin pull-right">                      
                        {{ $listData->appends(request()->input())->links() }}
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