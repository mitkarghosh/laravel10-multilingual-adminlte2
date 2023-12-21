@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.avatar.list')}}"><i class="fa fa-user" aria-hidden="true"></i> @lang('custom_admin.lab_avatar_list')</a></li>
        <li class="active">{{ $data['page_title'] }}</li>
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
                                    'route' => ['admin.'.\App::getLocale().'.avatar.editsubmit', $details["id"]],
                                    'title'  => 'editAvatarForm',
                                    'id'    => 'editAvatarForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    {{ Form::text('title', $details['title'], array(
                                                                'id' => 'title',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                 )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_title_dutch')<span class="red_star">*</span></label>
                                    {{ Form::text('title_de',$details->local[1]['local_title'], array(
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
                                    <label for="image">@lang('custom_admin.lab_image')</label><br>                                        
                                    {{ Form::file('image', array(
                                                                'id' => 'image',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                            )) }}
                                </div>
                                <span>@lang('custom_admin.lab_file_dimension') {{AdminHelper::ADMIN_AVATAR_THUMB_IMAGE_WIDTH}}px X {{AdminHelper::ADMIN_AVATAR_THUMB_IMAGE_HEIGHT}}px</span>
                                @if($details->image)
                                <div class="form-group">						
                                    @if(file_exists(public_path('/uploads/avatar/'.$details->image))) 
                                        <embed src="{{ asset('uploads/avatar/'.$details->image) }}"  height=50 />
                                    @endif						
                               </div>
                               @endif
                            </div>
                        </div>
                    </div>                    
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary" title="@lang('custom_admin.btn_update')">@lang('custom_admin.btn_update')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.avatar.list').'?page='.$data['pageNo'] }}" title="@lang('custom_admin.btn_cancel')" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection