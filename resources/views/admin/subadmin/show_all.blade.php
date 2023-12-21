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
					<div class="row">
					{{ Form::open(array(
									'method' => 'GET',
									'class' => '',
									'route' =>  ['admin.'.\App::getLocale().'.subAdmin.show-all'],
									'id' => 'searchSubadminForm',
									'novalidate' => true)) }}                          
						
						<div class="col-md-12">
							<div class="col-md-3">
								<label>Name</label>
								<div class="">
									<input type="text" class="form-control"  name="profile_name" placeholder="Type Name"  value="{{ $searchData['profileName'] }}">       
								</div>
							</div>
							<div class="col-md-3">
								<label>Mobile </label>
								<select class="form-control select2" name="mobile_no">
									<option value="">Select</option>
									@foreach($userDropdown as $user)
										<option value="{{$user->phone_no}}" @if($user->phone_no==$searchData['mobileNo']) selected="selected" @endif >{{$user->phone_no}}</option>
									@endforeach 
							</select>
							</div>
							<div class="col-md-3">
								<label>Email</label>
								<select class="form-control select2"  name="email" id="email">
									<option value="">Select</option>
									@foreach($userDropdown as $v_user)
										<option value="{{$v_user->email}}" @if($v_user->email==$searchData['email']) selected="selected"  @endif >{{$v_user->email}}</option>
									@endforeach 
								</select>
							</div>
							<div class="col-md-3">
								<label>@lang('custom_admin.lab_subadmin_role')</label>
								<select class="form-control select2" name="role_id[]" multiple="multiple">
									@foreach($roleList as $role)
										<option value="{{$role->id}}" @if(in_array($role->id, $searchData['roleIds'])) selected="selected" @endif >{{$role->name}}</option>
									@endforeach 
								</select>
							</div>
						</div>					

						<div class="col-md-12">
							<div class="col-md-3">
								<label>&nbsp;</label><br>
								<button type="submit" class="btn btn-info top_margin"><i class="fa fa-search"></i></button>
								<a href="{{ route('admin.'.\App::getLocale().'.subAdmin.show-all') }}" class="btn btn-default"><i class="fa fa-refresh"></i></a>
							</div>
						</div>
						
					{!! Form::close() !!}
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
							<th>@lang('custom_admin.lab_order_contact_number')</th>
							<th>@lang('custom_admin.lab_subadmin_role')</th>
                            <th>@lang('custom_admin.lab_status')</th>
						  	<th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                      </tr>
                    @if(count($list) > 0)
                      @foreach ($list as $row)
                      <tr>
						<td>{{ $row->first_name }}</td>
						<td>{{ $row->last_name }}</td>
						<td>{{ $row->email }}</td>
						<td>{{ $row->phone_no }}</td>
						<td>
						@if ($row->userRoles)
							@foreach ($row->userRoles as $role)
								<span class="label label-info">{{$role->name}}</span><br />
							@endforeach
						@endif
						</td>
                        <td>
                            <span class="label @if($row->status == 1) label-success @else label-danger @endif">
                            @if($row['status'] == '1')
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.subAdmin.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_active')
                                </a>
                            @else
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.subAdmin.change-status', [$row->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_inactive')
                                </a>
                            @endif
                            </span>
                        </td>
                          <td class="text_align_center">
                            <a href="{{ route('admin.'.\App::getLocale().'.subAdmin.edit', [$row->id]) }}" title="@lang('custom_admin.lab_edit')" class="btn btn-info btn-sm">
                              <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="@lang('custom_admin.lab_delete')" data-href="{{ route('admin.'.\App::getLocale().'.subAdmin.delete', [$row->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a> 
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
                                <a class="btn btn-primary" href="{{route('admin.'.\App::getLocale().'.subAdmin.list')}}">@lang('custom_admin.label_pageview')</a>
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