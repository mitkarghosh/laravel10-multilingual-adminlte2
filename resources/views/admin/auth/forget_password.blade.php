@extends('admin.layouts.login', ['title' => $page_title])

@section('content')

    <!-- <p class="login-box-msg">{{ $page_title }}</p> -->

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

    {!! Form::open(array('name'=>'adminForgotPasswordForm','route' =>  ['admin.'.\App::getLocale().'.forget-password'], 'id' => 'adminForgotPasswordForm')) !!}
        <div class="form-group has-feedback">
            {{ Form::text('email', null, array('required','class' => 'form-control','id' => 'email', 'placeholder' => trans('custom_admin.placeholder_email'))) }}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <button type="submit" class="btn btn-primary btn-block btn-flat btn-submit">@lang('custom_admin.btn_submit')</button>
                <a href="{{ \URL::route('admin.'.\App::getLocale().'.login') }}" style="display:inline-block;margin-top:10px;">@lang('custom_admin.label_back_to_login')</a>
            </div>
        </div>
    {!! Form::close() !!}

@endsection