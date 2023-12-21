@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">{{ $data['page_title'] }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Password</h3>
                </div>

                <div class="box-header">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <div class="alert alert-dismissable alert-{{ $msg }}">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <span>{{ Session::get('alert-' . $msg) }}</span><br/>
                            </div>
                        @endif
                    @endforeach

                    @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span><br/>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.user.change-password', $data["details"]["id"]],
                                    'name'  => 'updateUserPassword',
                                    'id'    => 'updateUserPassword',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NewPassword">New Password<span class="red_star">*</span></label>
                                    {{ Form::password('password', array(
                                                                'id' => 'password',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'New Password',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ConfirmPassword">Confirm Password<span class="red_star">*</span></label>
                                    {{ Form::password('confirm_password', array(
                                                                'id' => 'confirm_password',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Confirm Password',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            @if ($dataUser['role_id'] == null)
                                <a href="{{ route('admin.user.list-frontend') }}" class="btn btn-block btn-default btn_width_reset">Cancel</a>
                            @else
                                <a href="{{ route('admin.user.list-adminend') }}" class="btn btn-block btn-default btn_width_reset">Cancel</a>
                            @endif
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