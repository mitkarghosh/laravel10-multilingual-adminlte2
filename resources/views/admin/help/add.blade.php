@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.help.list')}}"><i class="fa fa-question-circle" aria-hidden="true"></i> @lang('custom_admin.lab_help')</a></li>
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
                                    'route' => ['admin.'.\App::getLocale().'.help.addsubmit'],
                                    'name'  => 'addHelpForm',
                                    'id'    => 'addHelpForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    {{ Form::text('title', null, array(
                                                                'id' => 'title',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titleArabic">@lang('custom_admin.lab_title_dutch')<span class="red_star">*</span></label>
                                    {{ Form::text('title_de', null, array(
                                                                'id' => 'title_de',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Name">@lang('custom_admin.lab_description')<span class="red_star">*</span></label>
                                    {{ Form::textarea('description_en', null, array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_description_dutch')<span class="red_star">*</span></label>
                                    {{ Form::textarea('description_de', null, array(
                                                                'id'=>'description_de',
                                                                'class' => 'form-control',
                                                                'required' => 'required')) }}
                                </div>
                            </div>
                        </div>                        
                    </div>                        
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.help.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script type="text/javascript">
$(function () {
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_de');
});
</script>

@endsection