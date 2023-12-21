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
        <div class="col-md-12">
            <div class="box box-primary">
                @include('admin.elements.notification')
            
                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.user.addsubmit'],
                                    'name'  => 'addAdminUserProfile',
                                    'id'    => 'addAdminUserProfile',
                                    'files' => true,
                                    'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">Full Name<span class="red_star">*</span></label>
                                    {{ Form::text('full_name', null, array(
                                                                'id' => 'full_name',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Full Name',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="LastName">Email<span class="red_star">*</span></label>
                                    {{ Form::text('email', null, array(
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
                                    {{ Form::text('phone_no', null, array(
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
                                                                'placeholder' => 'Password',
                                                                'required' => 'required' )) }}
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
<script>
    $(()=>{
        $(".user_type_select").change(function() {
            $('#role_div').removeClass('hidden');
            if($(this).val() == '2') {
                $('.role_ids').prop('checked', false);
                $(".back_role").removeClass('hidden');
                $(".front_role").addClass('hidden');
            } else if ($(this).val() == '1') {
                $(".back_role")[0].selectedIndex = 0;
                $(".back_role").addClass('hidden');
                $(".front_role").removeClass('hidden');
            } else {
                $('.role_ids').prop('checked', false);
                $(".back_role")[0].selectedIndex = 0;
                $('#role_div').addClass('hidden');
            }
        });

        $('.role_ids').on('click', function(ev){
            if ($(this).val() == 'customer') {
                ev.preventDefault();
            }
        });
    })
    </script>
@endsection