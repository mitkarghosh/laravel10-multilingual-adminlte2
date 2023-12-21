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

                <div class="box-body table-responsive">
                  <table class="table table-bordered">
                    <tr>
                        <th>No.</th>
                        <th>@lang('custom_admin.lab_title')</th>
                        <th>@lang('custom_admin.lab_title_dutch')</th>
                        <th>@lang('custom_admin.lab_status')</th>
                        <th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                    </tr>
                     @php $i=1 @endphp
                      @if(count($list) > 0)
                          @foreach ($list as $row)
                            <tr>
                                <td>
                                  {{$i}}
                                  @php $i++ @endphp
                                </td>
                                <td>{{ $row->en_title }}</td>
                                <td>{{ $row->de_title }}</td>
                                <td>
                                <span class="label @if($row->status == 1) label-success @else label-danger @endif">
                                @if($row->status == '1')
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.product.addon-change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_active')
                                </a>
                                @else
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.product.addon-change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                @lang('custom_admin.lab_inactive')
                                </a>
                                @endif
                                </span>
                                </td>

                                <td class="text_align_center">
                                  <a href="{{ route('admin.'.\App::getLocale().'.product.edit-addon') }}/{{ $row->id }}" title="Edit" class="btn btn-info btn-sm">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                                  </a>
                                  &nbsp;
                                  <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.product.delete-addon', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                </td>
                            </tr>
                          @endforeach
                      @endif  
                  </table>
                </div>
              
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</section>
<!-- /.content -->

@endsection