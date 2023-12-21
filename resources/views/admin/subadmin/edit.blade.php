@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.subAdmin.list')}}"><i class="fa fa-user-plus" aria-hidden="true"></i> @lang('custom_admin.lab_subadmin_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                @include('admin.elements.notification')
                
                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.subAdmin.editsubmit', $id],
                                    'name'  => 'editSubAdminForm',
                                    'id'    => 'editSubAdminForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.label_first_name')<span class="red_star">*</span></label>
                                    {{ Form::text('first_name', $details->first_name, array(
                                                                'id' => 'first_name',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titleArabic">@lang('custom_admin.label_last_name')<span class="red_star">*</span></label>
                                    {{ Form::text('last_name', $details->last_name, array(
                                                                'id' => 'last_name',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.label_email')<span class="red_star">*</span></label>
                                    {{ Form::text('email', $details->email, array(
                                                                'id' => 'email',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titleArabic">@lang('custom_admin.lab_order_contact_number')<span class="red_star">*</span></label>
                                    {{ Form::text('phone_no', $details->phone_no, array(
                                                                'id' => 'phone_no',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                @php
                                $selectedRoles = old('role');
                                if($selectedRoles == null)$selectedRoles = [];
                                @endphp
                                <div class="form-group">
                                    <label>@lang('custom_admin.lab_subadmin_role')<span class="red_star">*</span></label>
                                    <select class="form-control select2" id="role" name="role[]" multiple="multiple">
                                @if (count($roleList) > 0)
                                    @foreach ($roleList as $role)
                                        <option value="{{$role->id}}" @if(in_array($role->id,$selectedRoles) || in_array($role->id, $roleIds)) selected="selected" @endif >{{$role->name}}</option>
                                    @endforeach
                                @endif
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>                        
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_update')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.subAdmin.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->


@endsection