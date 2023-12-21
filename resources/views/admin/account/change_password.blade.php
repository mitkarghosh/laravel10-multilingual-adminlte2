@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('custom_admin.lab_password')</h3>
                </div>

                <div class="box-header">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <div class="alert alert-dismissable alert-{{ $msg }}">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <span>{{ Session::get('alert-' . $msg) }}</span><br/>
                            </div>
                            <script>
                            $(document).ready(function(){
                                setTimeout(function () {
                                    <?php
                                    if($msg == 'success') {
                                        $url = route('admin.'.\App::getLocale().'.logout');
                                    ?>
                                        window.location.href = '<?php echo $url;?>';
                                    <?php
                                    }
                                    ?>
                                }, 2000);
                            });                            
                            </script>
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
                                    'route' => ['admin.'.\App::getLocale().'.change-password'],
                                    'name'  => 'updateAdminPassword',
                                    'id'    => 'updateAdminPassword',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="OldPassword">@lang('custom_admin.lab_current_password')<span class="red_star">*</span></label>
                                    {{ Form::password('current_password', array(
                                                                'id' => 'current_password',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NewPassword">@lang('custom_admin.lab_new_password')<span class="red_star">*</span></label>
                                    {{ Form::password('password', array(
                                                                'id' => 'password',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ConfirmPassword">@lang('custom_admin.lab_confirm_password')<span class="red_star">*</span></label>
                                    {{ Form::password('confirm_password', array(
                                                                'id' => 'confirm_password',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                                <a href="{{ route('admin.'.\App::getLocale().'.dashboard') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
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