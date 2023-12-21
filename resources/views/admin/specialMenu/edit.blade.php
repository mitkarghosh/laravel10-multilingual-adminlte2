@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.specialMenu.list')}}"><i class="fa fa-heart"></i>@lang('custom_admin.lab_special_menu_list')</a></li>
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
                                    'route' => ['admin.'.\App::getLocale().'.specialMenu.editsubmit', $details["id"]],
                                    'title'  => 'updateSpecialMenuForm',
                                    'id'    => 'updateSpecialMenuForm',
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
                                    <label for="title">@lang('custom_admin.lab_description')<span class="red_star">*</span></label>
                                    {{ Form::textarea('description_en',$details->local[0]['local_description'], array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control',
                                                                'rows' => 3,
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titleArabic">@lang('custom_admin.lab_description_dutch')<span class="red_star">*</span></label>
                                    {{ Form::textarea('description_de', $details->local[1]['local_description'], array(
                                                                'id'=>'description_de',
                                                                'class' => 'form-control',
                                                                'rows' => 3,
                                                                'required' => 'required')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">                                    
                                    <label for="Amount">@lang('custom_admin.lab_price')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::text('price', $details->price, array(
                                                                        'id' => 'price',
                                                                        'min' => 0,
                                                                        'placeholder' => '',
                                                                        'class' => 'form-control',
                                                                        'required' => 'required'
                                                                        )) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">@lang('custom_admin.lab_image')</label><br>                                        
                                    {{ Form::file('image', array(
                                                                'id' => 'image',
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Upload Image',
                                                            )) }}
                                </div>
                                <span>@lang('custom_Admin.lab_file_dimension') {{AdminHelper::ADMIN_SPECIAL_MENU_THUMB_IMAGE_WIDTH}}px X {{AdminHelper::ADMIN_SPECIAL_MENU_THUMB_IMAGE_HEIGHT}}px</span>
                                @if($details->image)
                                <div class="form-group">						
							        @if(file_exists(public_path('/uploads/special_menu/'.$details->image))) 
								        <embed src="{{ asset('uploads/special_menu/'.$details->image) }}"  height=50 />
							        @endif						
					           </div>
					           @endif
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_update')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.specialMenu.list').'?page='.$data['pageNo'] }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
{{-- <script>
$(function () {
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_de');
})
</script> --}}
@endsection