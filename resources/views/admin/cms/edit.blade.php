@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.CMS.list')}}"><i class="fa fa-database" aria-hidden="true"></i> @lang('custom_admin.lab_cms_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $panel_title }}</h3>
                </div>

                @include('admin.elements.notification')

                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.CMS.editsubmit', $details->id],
                                    'name'  => 'updateCmsForm',
                                    'id'    => 'updateCmsForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Name">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    {{ Form::text('title_en', $details->local[0]->title, array(
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_title_dutch')<span class="red_star">*</span></label>
                                    {{ Form::text('title_de', $details->local[1]->title, array(
                                                                'class' => 'form-control',
                                                                'placeholder' => '  ',
                                                                'required' => 'required')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="Name">@lang('custom_admin.lab_description')</label>
                                    {{ Form::textarea('description_en', $details->local[0]->description, array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_description_dutch')</label>
                                    {{ Form::textarea('description_de', $details->local[1]->description, array(
                                                                'id'=>'description_de',
                                                                'class' => 'form-control',
                                                                'required' => 'required')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Name">Meta Keyword<span class="red_star">*</span></label>
                                    {{ Form::text('meta_keyword', $details->meta_keyword, array(
                                                                'class' => 'form-control' )) }}
                                </div>
                                <?php /*<div class="form-group">
                                    <label for="Name">Banner Photo</label>
                                    {{ Form::file('banner',  array('class' => 'form-control' )) }}
                                </div>*/ ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">Meta Description<span class="red_star">*</span></label>
                                    {{ Form::textarea('meta_description', $details->meta_description, array(
                                                                'style'=>'height:100px;',
                                                                'class' => 'form-control')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_update')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.CMS.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<script>
    $(function () {
        CKEDITOR.replace('description_en');
        CKEDITOR.replace('description_de');
    })
</script>
@endsection