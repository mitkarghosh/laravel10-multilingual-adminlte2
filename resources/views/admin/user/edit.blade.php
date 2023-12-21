@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.user.list')}}"><i class="fa fa-users" aria-hidden="true"></i> User List</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">    
        {{ Form::open(array(
                            'method'=> 'POST',
                            'class' => '',
                            'route' => ['admin.'.\App::getLocale().'.user.editsubmit', $details->id],
                            'name'  => 'updateAdminUserProfile',
                            'id'    => 'updateAdminUserProfile',
                            'files' => true,
                            'novalidate' => true)) }}
        <div class="col-md-12">
            <div class="box box-primary">
                @include('admin.elements.notification')
                    
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FullName">Full Name<span class="red_star">*</span></label>
                                    {{ Form::text('full_name', $details->full_name, array(
                                                                'id' => 'full_name',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Full Name',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="LastName">Email<span class="red_star">*</span></label>
                                    {{ Form::text('email', $details->email, array(
                                                                    'id' => 'email',
                                                                    'class' => 'form-control',
                                                                    'placeholder' => 'Email',
                                                                    'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">Phone Number<span class="red_star">*</span></label>
                                    {{ Form::text('phone_no', $details->phone_no, array(
                                                                'id' => 'phone_no',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Phone Number',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Password">Password<span class="red_star">*</span></label>
                                    {{ Form::password('password', array(
                                                                'id' => 'password',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Password' )) }}
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Submit</button>                            
                                <a href="{{ route('admin.'.\App::getLocale().'.user.list') }}" class="btn btn-block btn-default btn_width_reset">Cancel</a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>        
    </div>
</section>
<!-- /.content -->

@endsection