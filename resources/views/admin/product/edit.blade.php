@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')
@php $is_addon_id=0; @endphp
@if ($details->is_menu == 'Y')

@php  
if (count($details->productMenuTitles) > 0) {
            $addonids=0;                            
            foreach($details->productMenuTitles as $keyMenuTitle => $valMenuTitle) {
                   
                    if($valMenuTitle->addon_id && $addonids < 1){
                        $addonids=1;  
                        $is_addon_id=1;     
                    }
                    $addonids++;
            }

}
@endphp 
@endif
@if ($is_addon_id<1 && $details->is_menu == 'Y')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.product.list')}}"><i class="fa fa-product-hunt" aria-hidden="true"></i> @lang('custom_admin.lab_product_list')</a></li>
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
                                    'route' => ['admin.'.\App::getLocale().'.product.editsubmit', $details["id"]],
                                    'id'    => 'editProductForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                                    <input type="hidden" name="producttype" value="old">
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
                                    <label for="Name">@lang('custom_admin.lab_description')</label>
                                    {{ Form::textarea('description_en', $details->local[0]['local_description'], array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_description_dutch')</label>
                                    {{ Form::textarea('description_de', $details->local[1]['local_description'], array(
                                                                'id'=>'description_de',
                                                                'class' => 'form-control')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_category')<span class="red_star">*</span></label>
                                    <select name="category_id" id="category_id" required class="form-control select2">
                                        <option value="">-@lang('custom_admin.lab_select')-</option>
                                @if (count($data['categoryList']))
                                    @foreach ($data['categoryList'] as $keyCategory => $valCategory)
                                        <option value="{{$keyCategory}}" @if($keyCategory == $details->category_id) selected="selected" @endif>{{$valCategory}}</option>
                                    @endforeach
                                @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">@lang('custom_admin.lab_image')</label><br>                                        
                                    {{ Form::file('image', array(
                                                                'id' => 'image',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                            )) }}
                                </div>
                                <span>@lang('custom_admin.lab_file_dimension') {{AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH}}px X {{AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT}}px</span>
                                @if($details->image)
                                <div class="form-group">						
                                    @if(file_exists(public_path('/uploads/product/'.$details->image))) 
                                        <embed src="{{ asset('uploads/product/'.$details->image) }}"  height="50" />
                                    @endif						
                               </div>
                               @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_is_menu')</label>
                                    @php
                                    $chk = '';
                                    if ($details->is_menu == 'Y') {
                                        $chk = 'checked';
                                    }
                                    @endphp
                                    <div>
                                        <label class="form-check-label">
                                            <input type="checkbox" id="is_menu" name="is_menu" value="Y" autocomplete="off" class="form-check-input" $chk>&nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_has_attribute')<span class="red_star">*</span></label>
                                    <div class="row">
                                        @php
                                        $checkedStatusYes = $checkedStatusNo = null;
                                        if ($details->has_attribute == 'Y') {
                                            $checkedStatusYes = 'checked';
                                        }
                                        else if ($details->has_attribute == 'N') {
                                            $checkedStatusNo = 'checked';
                                        }
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'Y', null, array($checkedStatusYes, 'class'=>'form-check-input has_attribute', 'id' => 'attr_1')) !!}
                                                    &nbsp;@lang('custom_admin.lab_yes')
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'N', null, array($checkedStatusNo, 'class'=>'form-check-input has_attribute', 'id' => 'attr_2')) !!}
                                                    &nbsp;@lang('custom_admin.lab_no')
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_show_ingredient')</label>
                                    <div>
                                        @php
                                        $ingredientCheckedStatusYes = null;
                                        if ($details->show_ingredients == 'Y') {
                                            $ingredientCheckedStatusYes = 'checked';
                                        }
                                        @endphp
                                        <label class="form-check-label">
                                            {!! Form::checkbox('show_ingredients', 'Y', null, array($ingredientCheckedStatusYes, 'id' => 'show_ingredients', 'class'=>'form-check-input flat-red')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group has_attribute_show" @if ($details->has_attribute == 'Y') style="display: block;" @else style="display: none;" @endif>
                                    <label for="title">@lang('custom_admin.lab_create_attribute')<span class="red_star">*</span></label>
                                    @php
                                    if (count($details->productAttributes) > 0) {
                                        $countProductAttribute = count($details->productAttributes);
                                    } else {
                                        $countProductAttribute = 1;
                                    }
                                    @endphp
                                    
                                    <input type="hidden" id="attrib_count" value="{{$countProductAttribute}}">

                                    <div class="addField">
                                @php
                                $k = 1; $m = 0;
                                if(count($details->productAttributes) > 0) {
                                    foreach($details->productAttributes as $key => $attribute) {
                                @endphp
                                        <div class="row" style="margin-top: 10px;">
                                            {!! Form::hidden('attr_id[]', $attribute->id, array('required', 'class'=>'form-control')) !!}

                                            <div class="col-md-4">
                                            @if ($k == 1)
                                                <label for="title">@lang('custom_admin.lab_english')</label>
                                            @endif
                                                {!! Form::text('attr_title_en['.$key.']', $attribute->local[0]->local_title, array('required', 'class'=>'form-control', 'placeholder' => 'Title (English)', 'id' => 'attr_title_en'.$m)) !!}
                                            </div>
                                            <div class="col-md-4">
                                            @if ($k == 1)
                                                <label for="title">@lang('custom_admin.lab_dutch')</label>
                                            @endif
                                                {!! Form::text('attr_title_de['.$key.']', $attribute->local[1]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_de'.$m)) !!}
                                            </div>
                                            <div class="col-md-2">
                                            @if ($k == 1)
                                                <label for="title">&nbsp;</label>
                                            @endif
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        CHF
                                                    </div>
                                                    {!! Form::number('attr_price', $attribute->price, array('required','class'=>'form-control', 'placeholder' => '', 'name'=>'attr_price['.$key.']', 'id' => 'attr_price'.$m, 'min' => 0)) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                            @if ($k == 1)
                                                <label for="title">&nbsp;</label><br />
                                            @endif
                                                <button class="btn btn-danger attribute_move_to_trash" data-productid="{{$details['id']}}" data-attributeid="{{$attribute->id}}" type="button"><i class="fa fa-trash"></i></button>
                                                <button class="btn btn-info attribute_change_status" data-productid="{{$details['id']}}" data-attributeid="{{$attribute->id}}" type="button">
                                                @if ($attribute->status == '1')
                                                    <i class="fa fa-unlock"></i>
                                                @else
                                                <i class="fa fa-lock"></i>
                                                @endif                                                    
                                                </button>
                                            @if ($k == 1)
                                                <button class="btn btn-success add-more" id="addrow" type="button"><i class="fa fa-plus"></i></button>
                                            @endif
                                            </div>
                                        </div>
                            @php
                                    $k++; $m++;
                                    }
                                } else {
                            @endphp

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="title">@lang('custom_admin.lab_english')</label>
                                            {!! Form::text('attr_title_en[0]', null, array('class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_en0')) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label for="title">@lang('custom_admin.lab_dutch')</label>
                                            {!! Form::text('attr_title_de[0]', null, array('class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_de0')) !!}
                                        </div>
                                        <div class="col-md-2">
                                            <label for="title">&nbsp;</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    CHF
                                                </div>
                                                {!! Form::number('attr_price', null, array('class'=>'form-control', 'placeholder' => '', 'name'=>'attr_price[]', 'id' => 'attr_price0', 'min' => 0)) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="title">&nbsp;</label><br />
                                            <button class="btn btn-success add-more" id="addrow" type="button"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                            @php
                                }
                            @endphp
                                    </div>
                                </div>

                                <div class="form-group no_attribute_show" @if ($details->has_attribute == 'N') style="display: block;" @else style="display: none;" @endif>
                                    <label for="title">@lang('custom_admin.lab_price')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::number('price', $details->price, array(
                                                                'id' => 'price',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'min' => 0
                                                                )) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group is_menu_show" @if ($details->is_menu == 'Y') style="display: block;" @else style="display: none;" @endif>
                                    <label for="title">@lang('custom_admin.lab_add_addon')<span class="red_star">*</span></label>

                                    @php
                                    if (count($details->productMenuTitles) > 0) {
                                        $countProductMenuTitles = count($details->productMenuTitles);
                                    } else {
                                        $countProductMenuTitles = 1;
                                    }
                                    @endphp
                                    
                                    <input type="hidden" id="pro_menu_title_count" value="{{$countProductMenuTitles}}">

                                    <div class="addDropdownField">
                                    @php
                                    $g = 1; $h = 0;
                                    if (count($details->productMenuTitles) > 0) {
                                        // dd($details->productMenuTitles);
                                        foreach($details->productMenuTitles as $keyMenuTitle => $valMenuTitle) {
                                            // dd($valMenuTitle->menuValues);
                                            $chkd = '';
                                            if ($valMenuTitle->is_multiple == 'Y') {
                                                $chkd = 'checked';
                                            }
                                    @endphp
                                        <div class="row maindropdown_{{$keyMenuTitle}}" style="margin-top: 10px;">
                                            {!! Form::hidden('dropdown['.$keyMenuTitle.'][id]', $valMenuTitle->id, array('required', 'class'=>'form-control')) !!}

                                            <!-- Main Grouping start -->
                                            <div class="col-md-10 menu_dropdown_option">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-5">
                                                            <label for="title">@lang('custom_admin.lab_title')</label>
                                                            {!! Form::text('dropdown['.$keyMenuTitle.'][title_en]', $valMenuTitle->local[0]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label for="title">@lang('custom_admin.lab_title_dutch')</label>
                                                            {!! Form::text('dropdown['.$keyMenuTitle.'][title_de]', $valMenuTitle->local[1]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="title">@lang('custom_admin.lab_is_multiple')</label>
                                                            <div>
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" name="dropdown[{{$keyMenuTitle}}][is_multiple]" value="Y" autocomplete="off" class="form-check-input dropDownBoxIsMultiple drop_down_is_multiple_{{$keyMenuTitle}}" data-dropbox="{{$keyMenuTitle}}" {{$chkd}}>
                                                                    &nbsp;@lang('custom_admin.lab_yes')
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">&nbsp;</div>

                                                <!-- Sub Grouping start -->
                                                <div class="addSubDropdownField_{{$keyMenuTitle}}">
                                                
                                                @foreach ($valMenuTitle->menuValues as $keyValue => $valValue)
                                                    <div class="row margin0" style="margin-top: 10px;">
                                                        {!! Form::hidden('dropdown['.$keyMenuTitle.'][val_id][]', $valValue->id, array('required', 'class'=>'form-control')) !!}

                                                        <div class="col-md-4">
                                                        @if ($keyValue == 0)
                                                            <label for="title">@lang('custom_admin.lab_dropdown_value')</label>
                                                        @endif
                                                            {!! Form::text('dropdown['.$keyMenuTitle.'][val_en][]', $valValue->local[0]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-4">
                                                        @if ($keyValue == 0)
                                                            <label for="title">@lang('custom_admin.lab_dropdown_value_dutch')</label>
                                                        @endif
                                                            {!! Form::text('dropdown['.$keyMenuTitle.'][val_de][]', $valValue->local[1]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                        @if ($keyValue == 0)
                                                            <label for="title">@lang('custom_admin.lab_price')</label>
                                                        @endif
                                                            <div class="input-group">
                                                                <div class="input-group-addon">
                                                                    CHF
                                                                </div>
                                                                <input required class="form-control addMenuRequired subDropDownPrice_{{$keyMenuTitle}}" placeholder="" name="dropdown[{{$keyMenuTitle}}][val_price][]" min="0" value="{{$valValue->price}}" type="number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">                                                    
                                                        @if ($keyValue == 0)
                                                            <label for="title">&nbsp;</label><br />
                                                            <a class="btn btn-danger delete_dropdown_values" data-dropdownkey="{{$valMenuTitle->id}}" data-dropdownvalue="{{$valValue->id}}" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                            <button class="btn btn-success add-more addSubDropdownrow" data-subblckid="{{$keyMenuTitle}}" type="button"><i class="fa fa-plus"></i></button>
                                                        @else
                                                            <a class="btn btn-danger delete_dropdown_values" data-dropdownkey="{{$valMenuTitle->id}}" data-dropdownvalue="{{$valValue->id}}" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                                </div>
                                                <!-- Sub Grouping end -->
                                            </div>
                                            
                                            <div class="col-md-1" style="width: 120px;">
                                                @if ($keyMenuTitle == 0)
                                                    <a class="btn btn-danger delete_dropdown_title" data-dropdowntitle="{{$valMenuTitle->id}}" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                    <button class="btn btn-success add-more" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                                @else
                                                    <a class="btn btn-danger delete_dropdown_title" data-dropdowntitle="{{$valMenuTitle->id}}" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                @endif
                                            </div>
                                            <!-- Main Grouping end -->
                                        </div>
                                    @php
                                        $g++; $h++;
                                        }
                                    } else {
                                    @endphp                                        
                                        <div class="row maindropdown_0">
                                            <!-- Main Grouping start -->
                                            <div class="col-md-10 menu_dropdown_option">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-5">
                                                            <label for="title">@lang('custom_admin.lab_title')</label>
                                                            {!! Form::text('dropdown[0][title_en]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label for="title">@lang('custom_admin.lab_title_dutch')</label>
                                                            {!! Form::text('dropdown[0][title_de]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="title">@lang('custom_admin.lab_is_multiple')</label>
                                                            <div>
                                                                <label class="form-check-label">
                                                                    {!! Form::checkbox('dropdown[0][is_multiple]', 'Y', null, array('class'=>'form-check-input dropDownBoxIsMultiple drop_down_is_multiple_0', 'data-dropbox' => '0')) !!}
                                                                    &nbsp;@lang('custom_admin.lab_yes')
                                                                    <i class="input-helper"></i>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">&nbsp;</div>

                                                <!-- Sub Grouping start -->
                                                <div class="addSubDropdownField_0">
                                                
                                                    <div class="row margin0">
                                                        <div class="col-md-4">
                                                            <label for="title">@lang('custom_admin.lab_dropdown_value')</label>
                                                            {!! Form::text('dropdown[0][val_en][]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="title">@lang('custom_admin.lab_dropdown_value_dutch')</label>
                                                            {!! Form::text('dropdown[0][val_de][]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="title">@lang('custom_admin.lab_price')</label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon">
                                                                    CHF
                                                                </div>
                                                                <input required class="form-control addMenuRequired subDropDownPrice_0" placeholder="" name="dropdown[0][val_price][]" min="0" value="0" type="number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="title">&nbsp;</label><br />
                                                            <button class="btn btn-success add-more addSubDropdownrow" data-subblckid="0" type="button"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- Sub Grouping end -->
                                            </div>
                                            
                                            <div class="col-md-1">
                                                <button class="btn btn-success add-more" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <!-- Main Grouping end -->
                                        </div>
                                    @php
                                    }
                                    @endphp

                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">                            
                                <div class="form-group">                                    
                                    <label for="categories">@lang('custom_admin.lab_tag')(s)</label>
                                    <select name="tags[]" id="tags" multiple="multiple" class="form-control select2">
                                    @if (count($data['tagList']) > 0)
                                        @foreach ($data['tagList'] as $keyTag => $valTag)
                                            <option value="{{$keyTag}}" @if (in_array($keyTag,$data['productTagIds'])) selected @endif>{{$valTag}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>                    
                    <div class="box-footer">
                        <div class="col-md-6 pl-0">
                            <button type="submit" class="btn btn-primary" title="@lang('custom_admin.btn_update')">@lang('custom_admin.btn_update')</button>
                            @if (Session::get('searchUrl') == '')
                            <a href="{{ route('admin.'.\App::getLocale().'.product.list').'?page='.$data['pageNo'] }}" title="@lang('custom_admin.btn_cancel')" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                            @else
                            <a href="{{ Session::get('searchUrl') }}" title="@lang('custom_admin.btn_cancel')" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                            @endif
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

    @if ($details->has_attribute == 'Y')
        $('#show_ingredients').attr('readonly',true);
        $("#show_ingredients").attr('disabled', 'disabled');
    @else
        $('#show_ingredients').removeAttr('readonly');
        $("#show_ingredients").removeAttr('disabled');
    @endif

    // Attribute section start //
    var counter = $('#attrib_count').val();
    $("#addrow").on("click", function () {
        if(counter < 5){
            var cols = '';
            var newRow = $('<div class="row" style="margin-top: 10px;">');
            cols += '<div class="col-sm-4"><input required class="form-control" placeholder="" id="attr_title_en'+counter+'" name="attr_title_en['+counter+']" type="text"></div>';
            cols += '<div class="col-sm-4"><input required class="form-control" placeholder="" id="attr_title_de'+counter+'" name="attr_title_de['+counter+']" type="text"></div>';
            cols += '<div class="col-md-2"><div class="input-group"><div class="input-group-addon">CHF</div><input required class="form-control" placeholder="" id="attr_price'+counter+'" name="attr_price['+counter+']" type="number" min=0></div></div>';
            cols += '<div class="col-sm-2"><a class="deleteRow btn btn-danger ibtnDel" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';

            newRow.append(cols);
            $(".addField").append(newRow);

            counter++;
        }else{
            Swal.fire({
                title: '{{trans("custom_admin.message_warning")}}!',
                text: '{{trans("custom_admin.message_maximum_attribute")}}',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    });
    $(".row").on("click", ".ibtnDel", function (event) {
        $(this).closest(".row").remove();
        counter--;
    });
    // Attribute section end //
    
    // Has attribute NO start //
    $(document).on('change', '.has_attribute', function() {
        if($('.has_attribute:checked').val() == 'Y'){
            $('.has_attribute_show').slideDown();
            $('.no_attribute_show').slideUp();
            $("input[id*=attr_price]").attr("required",true);
            $("input[id*=attr_title]").attr("required",true);
            
            $("#price").attr("required",false);

            $("#show_ingredients").prop('checked', false);
            $("#show_ingredients").attr('readonly', true);
            $("#show_ingredients").attr('disabled', 'disabled');
        }else{
            $("#price").attr("required",true);

            $("input[id*=attr_price]").attr("required",false);
            $("input[id*=attr_title]").attr("required",false);
            $('.has_attribute_show').slideUp();
            $('.no_attribute_show').slideDown();

            $("#show_ingredients").removeAttr('readonly');
            $("#show_ingredients").removeAttr('disabled');
        }
    });
    // Has attribute NO end //
    
    // change attribute status start //
    $(document).on('click', '.attribute_change_status', function() {
        var attributeId = $(this).data('attributeid');
        var productId = $(this).data('productid');
        
        Swal.fire({
			title: '{{trans("custom_admin.message_warning")}}!',
            text: '{{trans("custom_admin.lab_sure_to_change_status")}}',
            icon: 'warning',
            allowOutsideClick: false,
            // confirmButtonColor: '#d6f55b',
            // cancelButtonColor: '#141516',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
		}).then((result) => {
			if (result.value) {
				var changeAttributeStatusUrl = '{{ route("admin.".\App::getLocale().".product.change-status-product-attribute") }}';
                $('#whole-area').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: changeAttributeStatusUrl,
                    method: 'POST',
                    data: {
                        'attribute_id': attributeId,
                        'product_id': productId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $('#whole-area').hide(); //Showing loader

                        if (response.status == 1) {
                            $('.attribute_change_status > i').removeClass('fa-lock');
                            $('.attribute_change_status > i').addClass('fa-unlock');
                        } else {
                            $('.attribute_change_status > i').removeClass('fa-unlock');
                            $('.attribute_change_status > i').addClass('fa-lock');
                        }
                        
                        if(response.has_error == 0) {
                            Swal.fire({
                                title: "{{trans('custom_admin.message_success')}}",
                                text: response.message,
                                icon: "success",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });                            
                        } else{
                            Swal.fire({
                                title: "{{trans('custom_admin.message_error')}}",
                                text: response.message,
                                icon: "error",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });
                        }
                    }
                });
			}			
		});
    });
    // change attribute status end //
    
    // Delete attribute start //
    $(document).on('click', '.attribute_move_to_trash', function() {
        var attributeId = $(this).data('attributeid');
        var productId = $(this).data('productid');
        
        Swal.fire({
			title: '{{trans("custom_admin.message_warning")}}!',
            text: '{{trans("custom_admin.lab_want_delete")}}',
            icon: 'warning',
            allowOutsideClick: false,
            // confirmButtonColor: '#d6f55b',
            // cancelButtonColor: '#141516',
            showCancelButton: true,
            cancelButtonText: '{{trans("custom_admin.btn_cancel")}}',
            confirmButtonText: '{{trans("custom_admin.lab_yes")}}',
		}).then((result) => {
			if (result.value) {
				var deleteAttributeUrl = '{{ route("admin.".\App::getLocale().".product.delete-product-attribute") }}';
                $('#whole-area').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: deleteAttributeUrl,
                    method: 'POST',
                    data: {
                        'attribute_id': attributeId,
                        'product_id': productId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $('#whole-area').hide(); //Showing loader
                        
                        if(response.has_error == 0) {
                            Swal.fire({
                                title: "{{trans('custom_admin.message_success')}}",
                                text: response.message,
                                icon: "success",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            }).then((result) => {
                                window.location.reload();  
                            });                            
                        } else{
                            Swal.fire({
                                title: "{{trans('custom_admin.message_error')}}",
                                text: response.message,
                                icon: "error",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });
                        }
                    }
                });
			}			
		});
    });
    // Delete attribute end //

    // Is menu NO start //
    $('#is_menu').click(function(){
        if ($(this).prop("checked") == true) {
            $('#attr_2').trigger('click');
            $("#attr_1").attr('disabled', 'disabled');
            $("#show_ingredients").prop("checked", false);
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", true);
            $('.is_menu_show').slideDown();
        }
        else if ($(this).prop("checked") == false) {
            $("#attr_1").removeAttr('disabled');
            $('#attr_1').trigger('click');
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", false);
            $('.is_menu_show').slideUp();
        }
    });
    
    
    $('#is_menu').each(function(){
        if ($(this).prop("checked") == true) {
            $('#attr_2').trigger('click');
            $("#attr_1").attr('disabled', 'disabled');
            $("#show_ingredients").prop("checked", false);
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", true);
            $('.is_menu_show').slideDown();
        }
        else if ($(this).prop("checked") == false) {
            $("#attr_1").removeAttr('disabled');
            $('#attr_1').trigger('click');
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", false);
            $('.is_menu_show').slideUp();
        }
    });
    // Is menu NO end //

    // Dropdown section start //
    var mainBlockCounter = $('#pro_menu_title_count').val();
    var subBlockCounter = 0;
    $("#addDropdownRow").on("click", function () {
        if(mainBlockCounter < 10){
            mainBlockCounter++;
            subBlockCounter++;
            var cols = subcols = '';
            var newDropdownRow = $('<div class="row maindropdown_'+mainBlockCounter+'" style="margin-top: 10px;">');

            cols += '<div class="col-md-10 menu_dropdown_option">';
            cols += '<div class="row"><div class="col-md-12">';
            cols += '<div class="col-md-5"><label for="title">{{trans("custom_admin.lab_title")}}</label><input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][title_en]" type="text"></div>';
            cols += '<div class="col-md-5"><label for="title">{{trans("custom_admin.lab_title_dutch")}}</label><input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][title_de]" type="text"></div>';
            cols += '<div class="col-md-2"><label for="title">{{trans("custom_admin.lab_is_multiple")}}</label><div><label class="form-check-label"><input class="form-check-input dropDownBoxIsMultiple drop_down_is_multiple_'+mainBlockCounter+'" name="dropdown['+mainBlockCounter+'][is_multiple]" type="checkbox" value="Y" data-dropbox='+mainBlockCounter+'>&nbsp;&nbsp;Yes<i class="input-helper"></i></label></div></div>';
            cols += '</div></div>';            
            cols += '<div class="row">&nbsp;</div>';

            // dropdown Values start
            subcols += '<div class="addSubDropdownField_'+mainBlockCounter+'">';
                subcols += '<div class="row margin0">';
                subcols += '<div class="col-md-4">';
                subcols += '<label for="title">{{trans("custom_admin.lab_dropdown_value")}}</label>';
                subcols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][val_en][]" type="text">';
                subcols += '</div>';
                subcols += '<div class="col-md-4">';
                subcols += '<label for="title">{{trans("custom_admin.lab_dropdown_value_dutch")}}</label>';
                subcols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][val_de][]" type="text">';
                subcols += '</div>';
                subcols += '<div class="col-md-2">';
                subcols += '<label for="title">{{trans("custom_admin.lab_price")}}</label>';
                subcols += '<div class="input-group">';
                subcols += '<div class="input-group-addon">CHF</div>';
                subcols += '<input class="form-control addMenuRequired subDropDownPrice_'+mainBlockCounter+'" placeholder="" name="dropdown['+mainBlockCounter+'][val_price][]" min="0" value="0" type="number">';
                subcols += '</div>';
                subcols += '</div>';
                subcols += '<div class="col-md-2"><label for="title">&nbsp;</label><br><button class="btn btn-success add-more addSubDropdownrow" data-subblckid="'+mainBlockCounter+'" type="button"><i class="fa fa-plus"></i></button>';
                subcols += '</div>';
                subcols += '</div>';
            subcols += '</div>';
            // dropdown Values end

            cols += subcols;

            cols += '</div>';
            cols += '<div class="col-md-1"><a class="btn btn-danger deleteDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';            
                
            newDropdownRow.append(cols);
            $(".addDropdownField").append(newDropdownRow);
        }else{
            Swal.fire({
                // title: '{{trans("custom_admin.message_warning")}}!',
                text: "{{trans("custom_admin.message_maximum_dropdown")}}",
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    });
    $(".row").on("click", ".deleteDropdownRow", function (event) {
        $(this).closest(".row").remove();
        mainBlockCounter--;
    });
    // Dropdown section end //

    // Sub-Dropdown section start //
    var subCounter = 0;
    $(document).on("click", ".addSubDropdownrow", function () {
        subblckid = $(this).data('subblckid');        
        subCounter++;
        var subCols = '';
        var newSubRow = $('<div class="row margin0" style="margin-top: 10px;">');
        
        //subCols += '<div class="row margin0">';
        subCols += '<div class="col-md-4">';
        subCols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+subblckid+'][val_en][]" type="text">';
        subCols += '</div>';
        subCols += '<div class="col-md-4">';
        subCols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+subblckid+'][val_de][]" type="text">';
        subCols += '</div>';
        subCols += '<div class="col-md-2">';
        subCols += '<div class="input-group">';
        subCols += '<div class="input-group-addon">CHF</div>';
        subCols += '<input class="form-control addMenuRequired subDropDownPrice_'+subblckid+'" placeholder="" name="dropdown['+subblckid+'][val_price][]" min="0" value="0" type="number">';
        subCols += '</div>';
        subCols += '</div>';
        subCols += '<div class="col-md-2"><a class="btn btn-danger deleteSubDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
        //subCols += '</div>';

        newSubRow.append(subCols);
        $(".addSubDropdownField_"+subblckid).append(newSubRow);        
    });
    $(".row").on("click", ".deleteSubDropdownRow", function (event) {
        $(this).closest(".row").remove();
        subCounter--;
    });
    // Sub-Dropdown section end //

    // Delete dropdown Title start //
    $(document).on('click', '.delete_dropdown_title', function() {
        var dropdowntitleId = $(this).data('dropdowntitle');
        var productId = '{{$details["id"]}}';

        Swal.fire({
			// title: '{{trans("custom_admin.message_warning")}}!',
            text: '{{trans("custom_admin.lab_want_delete")}}',
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            cancelButtonText: '{{trans("custom_admin.btn_cancel")}}',
            confirmButtonText: '{{trans("custom_admin.lab_yes")}}',
		}).then((result) => {
			if (result.value) {
				var deleteProductDropdowTitleUrl = '{{ route("admin.".\App::getLocale().".product.delete-dropdown-title") }}';
                $('#whole-area').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: deleteProductDropdowTitleUrl,
                    method: 'POST',
                    data: {
                        'dropdowntitle_id': dropdowntitleId,
                        'product_id': productId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $('#whole-area').hide(); // Showing loader
                        
                        if(response.has_error == 0) {
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_success')}}",
                                text: response.message,
                                icon: "success",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            }).then((result) => {
                                window.location.reload();  
                            });                            
                        } else{
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_error')}}",
                                text: response.message,
                                icon: "error",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });
                        }
                    }
                });
			}
		});
    });
    // Delete dropdown key end //

    // Delete dropdown value start //
    $(document).on('click', '.delete_dropdown_values', function() {
        var dropdownkeyId = $(this).data('dropdownkey');
        var dropdownvalueId = $(this).data('dropdownvalue');
        var productId = '{{$details["id"]}}';

        Swal.fire({
			// title: '{{trans("custom_admin.message_warning")}}!',
            text: '{{trans("custom_admin.lab_want_delete")}}',
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            cancelButtonText: '{{trans("custom_admin.btn_cancel")}}',
            confirmButtonText: '{{trans("custom_admin.lab_yes")}}',
		}).then((result) => {
			if (result.value) {
				var deleteProductDropdowValueUrl = '{{ route("admin.".\App::getLocale().".product.delete-dropdown-values") }}';
                $('#whole-area').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: deleteProductDropdowValueUrl,
                    method: 'POST',
                    data: {
                        'dropdownkey_id': dropdownkeyId,
                        'dropdownvalue_id': dropdownvalueId,
                        'product_id': productId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $('#whole-area').hide(); // Showing loader
                        
                        if(response.has_error == 0) {
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_success')}}",
                                text: response.message,
                                icon: "success",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            }).then((result) => {
                                window.location.reload();  
                            });                            
                        } else{
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_error')}}",
                                text: response.message,
                                icon: "error",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });
                        }
                    }
                });
			}
		});
    });
    // Delete dropdown value end //
});
@if ($details->is_menu == 'Y')
$(document).ready(function(){
    $('#is_menu').click();
})
@endif
</script>

@else


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.product.list')}}"><i class="fa fa-product-hunt" aria-hidden="true"></i> @lang('custom_admin.lab_product_list')</a></li>
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
                                    'route' => ['admin.'.\App::getLocale().'.product.editsubmit', $details["id"]],
                                    'id'    => 'editProductForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                                    <input type="hidden" name="producttype" value="new">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    {{ Form::text('title',$details['title'], array(
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
                                    {{ Form::text('title_de', $details->local[1]['local_title'], array(
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
                                    <label for="Name">@lang('custom_admin.lab_description')</label>
                                    {{ Form::textarea('description_en', $details->local[0]['local_description'], array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_description_dutch')</label>
                                    {{ Form::textarea('description_de', $details->local[1]['local_description'], array(
                                                                'id'=>'description_de',
                                                                'class' => 'form-control')) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_category')<span class="red_star">*</span></label>
                                    <select name="category_id" id="category_id" required class="form-control select2">
                                        <option value="">-@lang('custom_admin.lab_select')-</option>
                                @if (count($categoryList))
                                    @foreach ($categoryList as $keyCategory => $valCategory)
                                        <option value="{{$keyCategory}}"  @if($keyCategory == $details->category_id) selected="selected" @endif @if($keyCategory == old('category_id') ) selected="selected" @endif>{{$valCategory}}</option>
                                    @endforeach
                                @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">@lang('custom_admin.lab_image')</label><br>
                                    {{ Form::file('image', array(
                                                                'id' => 'image',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                                <span>@lang('custom_admin.lab_file_dimension') {{AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH}}px X {{AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT}}px</span>
                                @if($details->image)
                                <div class="form-group">						
                                    @if(file_exists(public_path('/uploads/product/'.$details->image))) 
                                        <embed src="{{ asset('uploads/product/'.$details->image) }}"  height="50" />
                                    @endif						
                               </div>
                               @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_is_menu')</label>
                                    <div>
                                        <label class="form-check-label">
                                        @php
                                    $chk = '';
                                    if ($details->is_menu == 'Y') {
                                        $chk = 'checked';
                                    }
                                    @endphp
                                    <input type="checkbox" id="is_menu" name="is_menu" value="Y" autocomplete="off" class="form-check-input" $chk>&nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_has_attribute')<span class="red_star">*</span></label>
                                    <div class="row">
                                        @php
                                        $checkedStatusYes = $checkedStatusNo = null;
                                        if ($details->has_attribute == 'Y') {
                                            $checkedStatusYes = 'checked';
                                        }
                                        else if ($details->has_attribute == 'N') {
                                            $checkedStatusNo = 'checked';
                                        }
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'Y', null, array($checkedStatusYes, 'class'=>'form-check-input has_attribute', 'id' => 'attr_1')) !!}
                                                    &nbsp;@lang('custom_admin.lab_yes')
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'N', null, array($checkedStatusNo, 'class'=>'form-check-input has_attribute', 'id' => 'attr_2')) !!}
                                                    &nbsp;@lang('custom_admin.lab_no')
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_show_ingredient')</label>
                                    <div>
                                        @php
                                        $ingredientCheckedStatusYes = null;
                                        if ($details->show_ingredients == 'Y') {
                                            $ingredientCheckedStatusYes = 'checked';
                                        }
                                        @endphp
                                        <label class="form-check-label">
                                            {!! Form::checkbox('show_ingredients', 'Y', null, array($ingredientCheckedStatusYes, 'id' => 'show_ingredients', 'class'=>'form-check-input flat-red')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group has_attribute_show" @if ($details->has_attribute == 'Y') style="display: block;" @else style="display: none;" @endif>
                                    <label for="title">@lang('custom_admin.lab_create_attribute')<span class="red_star">*</span></label>
                                    @php
                                    if (count($details->productAttributes) > 0) {
                                        $countProductAttribute = count($details->productAttributes);
                                    } else {
                                        $countProductAttribute = 1;
                                    }
                                    @endphp
                                    
                                    <input type="hidden" id="attrib_count" value="{{$countProductAttribute}}">

                                    <div class="addField">
                                @php
                                $k = 1; $m = 0;
                                if(count($details->productAttributes) > 0) {
                                    foreach($details->productAttributes as $key => $attribute) {
                                @endphp
                                        <div class="row" style="margin-top: 10px;">
                                            {!! Form::hidden('attr_id[]', $attribute->id, array('required', 'class'=>'form-control')) !!}

                                            <div class="col-md-4">
                                            @if ($k == 1)
                                                <label for="title">@lang('custom_admin.lab_english')</label>
                                            @endif
                                                {!! Form::text('attr_title_en['.$key.']', $attribute->local[0]->local_title, array('required', 'class'=>'form-control', 'placeholder' => 'Title (English)', 'id' => 'attr_title_en'.$m)) !!}
                                            </div>
                                            <div class="col-md-4">
                                            @if ($k == 1)
                                                <label for="title">@lang('custom_admin.lab_dutch')</label>
                                            @endif
                                                {!! Form::text('attr_title_de['.$key.']', $attribute->local[1]->local_title, array('required', 'class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_de'.$m)) !!}
                                            </div>
                                            <div class="col-md-2">
                                            @if ($k == 1)
                                                <label for="title">&nbsp;</label>
                                            @endif
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        CHF
                                                    </div>
                                                    {!! Form::number('attr_price', $attribute->price, array('required','class'=>'form-control', 'placeholder' => '', 'name'=>'attr_price['.$key.']', 'id' => 'attr_price'.$m, 'min' => 0)) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                            @if ($k == 1)
                                                <label for="title">&nbsp;</label><br />
                                            @endif
                                                <button class="btn btn-danger attribute_move_to_trash" data-productid="{{$details['id']}}" data-attributeid="{{$attribute->id}}" type="button"><i class="fa fa-trash"></i></button>
                                                <button class="btn btn-info attribute_change_status" data-productid="{{$details['id']}}" data-attributeid="{{$attribute->id}}" type="button">
                                                @if ($attribute->status == '1')
                                                    <i class="fa fa-unlock"></i>
                                                @else
                                                <i class="fa fa-lock"></i>
                                                @endif                                                    
                                                </button>
                                            @if ($k == 1)
                                                <button class="btn btn-success add-more" id="addrow" type="button"><i class="fa fa-plus"></i></button>
                                            @endif
                                            </div>
                                        </div>
                            @php
                                    $k++; $m++;
                                    }
                                } else {
                            @endphp

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="title">@lang('custom_admin.lab_english')</label>
                                            {!! Form::text('attr_title_en[0]', null, array('class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_en0')) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label for="title">@lang('custom_admin.lab_dutch')</label>
                                            {!! Form::text('attr_title_de[0]', null, array('class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_de0')) !!}
                                        </div>
                                        <div class="col-md-2">
                                            <label for="title">&nbsp;</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    CHF
                                                </div>
                                                {!! Form::number('attr_price', null, array('class'=>'form-control', 'placeholder' => '', 'name'=>'attr_price[]', 'id' => 'attr_price0', 'min' => 0)) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="title">&nbsp;</label><br />
                                            <button class="btn btn-success add-more" id="addrow" type="button"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                            @php
                                }
                            @endphp
                                    </div>
                                </div>

                                <div class="form-group no_attribute_show" @if ($details->has_attribute == 'N') style="display: block;" @else style="display: none;" @endif>
                                    <label for="title">@lang('custom_admin.lab_price')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::number('price', $details->price, array(
                                                                'id' => 'price',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'min' => 0
                                                                )) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                      

                         

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group is_menu_show total-menu-item" data-count="{{count($addon_list)}}"  style="display: none;">
                                    <label for="title">@lang('custom_admin.lab_add_addon')<span class="red_star">*</span></label>
                                    <div class="addDropdownField">
                                    @php
                                    $g = 1; $h = 0;
                                    if (count($details->productMenuTitles) > 0) {
                                        // dd($details->productMenuTitles);
                                        foreach($details->productMenuTitles as $keyMenuTitle => $valMenuTitle) {
                                            // dd($valMenuTitle->menuValues);
                                            $chkd = '';
                                            if ($valMenuTitle->is_multiple == 'Y') {
                                                $chkd = 'checked';
                                            }
                                       @endphp
                                       <div class="row maindropdown_{{$keyMenuTitle}} mainrow-addons" data-id="0">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="collapse-type-heading">
                                                        <div class="col-md-4">   
                                                            <input required="" class="form-control" name="dropdown[{{$keyMenuTitle}}][id]" type="hidden" value="{{$valMenuTitle->id}}">
                                                            <select class="form-control main-addon-selection" required data-index="{{$keyMenuTitle}}" name="dropdown[{{$keyMenuTitle}}][main_addon]" data-addon_id="{{$valMenuTitle->addon_id}}" data-menu_id="{{$valMenuTitle->id}}">
                                                                <option value="">@lang('custom_admin.lab_select')</option>
                                                                @foreach($addon_list as $list)                                                              
                                                                    <option data-id="{{$list->id}}" value="{{$list->id}}" @if($valMenuTitle->addon_id==$list->id) selected  @endif data-de="{{$list->de_title }}" data-en="{{$list->en_title }}"> @if(App::getLocale() == 'de')  {{$list->de_title }} @else {{$list->en_title}}  @endif </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if($keyMenuTitle>0)
                                                        <i class="plus-minus-btn fa fa-angle-down" aria-hidden="true"></i>
                                                        <!-- <i class="plus-minus-btn fa fa-angle-up" aria-hidden="true"></i> -->                                                          
                                                        <!-- <a class="btn btn-danger deleteDropdownRow" href="javascript: void(0);">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a> -->
                                                        <a class="btn btn-danger delete_dropdown_title" data-dropdowntitle="{{$valMenuTitle->id}}" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        @endif
                                                        @if($keyMenuTitle<1)
                                                        <div class="without-selection-hide">
                                                            <button class="btn btn-success add-more" id="addDropdownRow" data-dropdowntitle="{{$valMenuTitle->id}}" type="button"><i class="fa fa-plus"></i></button>
                                                        
                                                            <i class="plus-minus-btn fa fa-angle-down" aria-hidden="true"></i>
                                                     
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="collapse-type-body ">
                                                    <div class="row">
                                                        {{-- Main Grouping start --}}
                                                        <div class="col-md-10 without-selection-hide menu_dropdown_option">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-5 en_main-title">
                                                                        <label for="title">@lang('custom_admin.lab_title')</label>
                                                                        {!! Form::text('dropdown['.$keyMenuTitle.'][title_en]', $valMenuTitle->local[0]->local_title, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-5 de_main-title">
                                                                        <label for="title">@lang('custom_admin.lab_title_dutch')</label>
                                                                        {!! Form::text('dropdown['.$keyMenuTitle.'][title_de]', $valMenuTitle->local[1]->local_title, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="title">@lang('custom_admin.lab_is_multiple')</label>
                                                                        <div>
                                                                            <label class="form-check-label">
                                                                                {!! Form::checkbox('dropdown[{{$keyMenuTitle}}][is_multiple]', 'Y', null, array('class'=>'form-check-input dropDownBoxIsMultiple drop_down_is_multiple_0', 'data-dropbox' => '0')) !!}
                                                                                &nbsp;@lang('custom_admin.lab_yes')
                                                                                <i class="input-helper"></i>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">&nbsp;</div>

                                                            {{-- Sub Grouping start --}}
                                                            <div class="addSubDropdownField_{{$keyMenuTitle}}">                                                
                                                                @foreach ($valMenuTitle->menuValues as $keyValue => $valValue)
                                                                {!! Form::hidden('dropdown['.$keyMenuTitle.'][val_id][]', $valValue->id, array('required', 'class'=>'form-control edit-drop-id')) !!}
                                                                    
                                                                    <div class="row margin0" style="margin-bottom:10px">
                                                                    <div class="col-md-2 checkbox-sub-div">
                                                                        <input class="checkbox-sub-value" type="hidden"   value="{{$valValue->sub_addon_status}}" name="dropdown[{{$keyMenuTitle}}][value_sub_addon_status][]">
                                                                        <input class="sub_new_addon_id" type="hidden" value="{{$valValue->sub_addon_id}}" name="dropdown[{{$keyMenuTitle}}][value_sub_addon_id][]">
                                                                        <input class="checkbox-sub" type="checkbox" @if($valValue->sub_addon_status) checked @endif> {{$valValue->local[0]->local_title}}
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input class="form-control addMenuRequired valid" value="{{$valValue->local[0]->local_title}}" placeholder="" name="dropdown[{{$keyMenuTitle}}][val_en][]" type="text" aria-invalid="false">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input class="form-control addMenuRequired" value="{{$valValue->local[1]->local_title}}" placeholder="" name="dropdown[{{$keyMenuTitle}}][val_de][]" type="text">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="input-group">
                                                                        <div class="input-group-addon">CHF</div>
                                                                        <input class="form-control addMenuRequired subDropDownPrice_0" placeholder="" name="dropdown[{{$keyMenuTitle}}][val_price][]" min="0" value="{{$valValue->price}}" type="number">
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            {{-- Sub Grouping end --}}
                                                        </div>
                                                        @if($keyMenuTitle<1)
                                                        <div class="col-md-1 without-selection-hide hide">
                                                            <button class="btn btn-success add-more" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                        @endif
                                                        {{-- Main Grouping end --}}
                                                        
                                                    </div>  
                                                </div>  
                                            </div>  
                                        </div>  
                                        
                                       @php
                                        $g++; $h++;
                                        }
                                    } else {
                                        @endphp
                                        <div class="row maindropdown_0 mainrow-addons" data-id="0">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="collapse-type-heading">
                                                        <div class="col-md-4">   
                                                            <select class="form-control main-addon-selection" data-index="0" name="dropdown[0][main_addon]">
                                                                <option value="">@lang('custom_admin.lab_select')</option>
                                                                @foreach($addon_list as $list)
                                                                    
                                                                    <option data-id="{{$list->id}}" value="{{$list->id}}" data-de="{{$list->de_title }}" data-en="{{$list->en_title }}"> @if(App::getLocale() == 'de')  {{$list->de_title }} @else {{$list->en_title}}  @endif </option>
                                                                    
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="collapse-type-body ">
                                                    <div class="row">
                                                        {{-- Main Grouping start --}}
                                                        <div class="col-md-10 without-selection-hide menu_dropdown_option hide">
                                                            <div class="row hide">
                                                                <div class="col-md-12">
                                                                    <div class="col-md-5 en_main-title">
                                                                        <label for="title">@lang('custom_admin.lab_title')</label>
                                                                        {!! Form::text('dropdown[0][title_en]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-5 de_main-title">
                                                                        <label for="title">@lang('custom_admin.lab_title_dutch')</label>
                                                                        {!! Form::text('dropdown[0][title_de]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="title">@lang('custom_admin.lab_is_multiple')</label>
                                                                        <div>
                                                                            <label class="form-check-label">
                                                                                {!! Form::checkbox('dropdown[0][is_multiple]', 'Y', null, array('class'=>'form-check-input dropDownBoxIsMultiple drop_down_is_multiple_0', 'data-dropbox' => '0')) !!}
                                                                                &nbsp;@lang('custom_admin.lab_yes')
                                                                                <i class="input-helper"></i>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">&nbsp;</div>

                                                            {{-- Sub Grouping start --}}
                                                            <div class="addSubDropdownField_0">                                                
                                                                <div class="row margin0">
                                                                    <div class="col-md-4">
                                                                        <label for="title">@lang('custom_admin.lab_dropdown_value')</label>
                                                                        {!! Form::text('dropdown[0][val_en][]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label for="title">@lang('custom_admin.lab_dropdown_value_dutch')</label>
                                                                        {!! Form::text('dropdown[0][val_de][]', null, array('class'=>'form-control addMenuRequired', 'placeholder' => '')) !!}
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="title">@lang('custom_admin.lab_price')</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon">
                                                                                CHF
                                                                            </div>
                                                                            <input required class="form-control addMenuRequired subDropDownPrice_0" placeholder="" name="dropdown[0][val_price][]" min="0" value="0" type="number">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="title">&nbsp;</label><br />
                                                                        <button class="btn btn-success add-more addSubDropdownrow" data-subblckid="0" type="button"><i class="fa fa-plus"></i></button>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            {{-- Sub Grouping end --}}
                                                        </div>
                                                        
                                                        <div class="col-md-1 without-selection-hide hide">
                                                            <button class="btn btn-success add-more" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                        {{-- Main Grouping end --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                    }
                                    @endphp
                                
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">                            
                                <div class="form-group">                                    
                                    <label for="categories">@lang('custom_admin.lab_tag')(s)</label>
                                    <select name="tags[]" id="tags" multiple="multiple" class="form-control select2">
                                    @if (count($data['tagList']) > 0)
                                        @foreach ($data['tagList'] as $keyTag => $valTag)
                                            <option value="{{$keyTag}}" @if (in_array($keyTag,$data['productTagIds'])) selected @endif>{{$valTag}}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                        
                    <div class="box-footer">
                        <div class="col-md-6 pl-0">
                            <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.product.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<select class="allsub-menus hide" data-lang="{{App::getLocale()}}">
    @foreach($sub_addon_list as $subs)
        <option data-parent="{{$subs->parent_id}}" data-price="{{$subs->price}}" data-lang="{{App::getLocale()}}" data-en="{{$subs->en_value }}" data-de="{{$subs->de_value }}" data-id="{{$subs->id}}">
            @if(App::getLocale() == 'de')  {{$subs->en_value }} @else {{$subs->de_value}} @endif
        </option>
    @endforeach
</select>
<script type="text/javascript">
$(function () {
 
    $('#show_ingredients').attr('readonly',true);
    $("#show_ingredients").attr('disabled', 'disabled');
  
    // Attribute section start //
    var counter = 0;
    $("#addrow").on("click", function () {
        if(counter < 4){
            counter++;
            var cols = '';
            var newRow = $('<div class="row" style="margin-top: 10px;">');
            cols += '<div class="col-sm-4"><input required class="form-control" placeholder="" id="attr_title_en'+counter+'" name="attr_title_en['+counter+']" type="text"></div>';
            cols += '<div class="col-sm-4"><input required class="form-control" placeholder="" id="attr_title_de'+counter+'" name="attr_title_de['+counter+']" type="text"></div>';
            cols += '<div class="col-md-2"><div class="input-group"><div class="input-group-addon">CHF</div><input required class="form-control" placeholder="" id="attr_price'+counter+'" name="attr_price['+counter+']" type="number" min=0></div></div>';
            cols += '<div class="col-sm-2"><a class="deleteRow btn btn-danger ibtnDel" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';

            newRow.append(cols);
            $(".addField").append(newRow);
        } else {
            Swal.fire({
                // title: '{{trans("custom_admin.message_warning")}}!',
                text: '{{trans("custom_admin.message_maximum_attribute")}}',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    });
    $(".row").on("click", ".ibtnDel", function (event) {
        $(this).closest(".row").remove();
        counter--;
    });
    // Attribute section end //
    
    // Dropdown section start //
    var mainBlockCounter = subBlockCounter = $(document).find('.main-addon-selection').length-1;
    $("#addDropdownRow").on("click", function () {

        var totdaladdon=Number($(document).find('.main-addon-selection').length);
        
        var totalitem=Number($(document).find('.total-menu-item').data('count'));
 

        if (totdaladdon<totalitem) {
            mainBlockCounter++;
            subBlockCounter++;
            var cols = subcols = '';
            var deleticn = '<a class="btn btn-danger deleteDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a><i class="plus-minus-btn fa fa-angle-up" aria-hidden="true"></i>';    
            var allmainoption="<div class='collapse-type-heading'><div class='col-md-12'><div class='row' style='margin-bottom:10px'><div class='col-md-4'><select data-index='"+mainBlockCounter+"' required name='dropdown["+mainBlockCounter+"][main_addon]' class='form-control main-addon-selection'>{!! $addon_list_dropdown !!}</select></div><div class='col-md-6'></div><div class='col-md-2 text-right pr-0'>"+deleticn+"</div></div></div></div>";

            var newDropdownRow = $('<div class="row mainrow-addons maindropdown_'+mainBlockCounter+'" data-id="'+mainBlockCounter+'" style="margin-top: 10px;">');
            cols += allmainoption;
            cols += '<div class="col-md-12"><div class="collapse-type-body "><div class="row"><div class="col-md-10 without-selection-hide hide menu_dropdown_option">';
            cols += '<div class="row"><div class="col-md-12">';
            cols += '<div class="col-md-5 en_main-title"><label for="title">{{trans("custom_admin.lab_title")}}</label><input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][title_en]" type="text"></div>';
            cols += '<div class="col-md-5 de_main-title"><label for="title">{{trans("custom_admin.lab_title_dutch")}}</label><input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][title_de]" type="text"></div>';
            cols += '<div class="col-md-2"><label for="title">{{trans("custom_admin.lab_is_multiple")}}</label><div><label class="form-check-label"><input class="form-check-input dropDownBoxIsMultiple drop_down_is_multiple_'+mainBlockCounter+'" name="dropdown['+mainBlockCounter+'][is_multiple]" type="checkbox" value="Y" data-dropbox='+mainBlockCounter+'>&nbsp;&nbsp;Yes<i class="input-helper"></i></label></div></div>';
            cols += '</div></div>';            
            cols += '<div class="row">&nbsp;</div>';
            

            cols += '<div class="addSubDropdownField_'+mainBlockCounter+'">';
                // subcols += '<div class="row margin0">';
                // subcols += '<div class="col-md-4">';
                // subcols += '<label for="title">{{trans("custom_admin.lab_dropdown_value")}}</label>';
                // subcols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][val_en][]" type="text">';
                // subcols += '</div>';
                // subcols += '<div class="col-md-4">';
                // subcols += '<label for="title">{{trans("custom_admin.lab_dropdown_value_dutch")}}</label>';
                // subcols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+mainBlockCounter+'][val_de][]" type="text">';
                // subcols += '</div>';
                // subcols += '<div class="col-md-2">';
                // subcols += '<label for="title">{{trans("custom_admin.lab_price")}}</label>';
                // subcols += '<div class="input-group">';
                // subcols += '<div class="input-group-addon">CHF</div>';
                // subcols += '<input class="form-control addMenuRequired subDropDownPrice_'+mainBlockCounter+'" placeholder="" name="dropdown['+mainBlockCounter+'][val_price][]" min="0" value="0" type="number">';
                // subcols += '</div>';
                // subcols += '</div>';
                // subcols += '<div class="col-md-2"><label for="title">&nbsp;</label><br><button class="btn btn-success add-more addSubDropdownrow" data-subblckid="'+mainBlockCounter+'" type="button"><i class="fa fa-plus"></i></button>';
                // subcols += '</div>';
                // subcols += '</div>';
                cols += '</div>';

            cols += '</div></div></div></div>';
            //cols += '<div class="col-md-1"><a class="btn btn-danger deleteDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';            
                
            newDropdownRow.append(cols);
            $(".addDropdownField").append(newDropdownRow);
            setKeyForManiAddon();
            resetMainaddonvalue();
        }else{
            var lng=$('.allsub-menus').data('lang');
            if(lng=='de'){
               message="Sie können nicht mehr als "+totdaladdon+" Dropdowns hinzufügen";
            }else{
                message="You won't be able to add more than "+totdaladdon+" dropdowns";
            }
           
            Swal.fire({
                // title: '{{trans("custom_admin.message_warning")}}!',
                text: message,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    });

    $(".row").on("click", ".deleteDropdownRow", function (event) {
        
        $(this).closest(".mainrow-addons").remove();
        mainBlockCounter--;
        setKeyForManiAddon();
        resetMainaddonvalue();
    });
     
    $(".row").on("click", ".deleteSubDropdownRow", function (event) {
        $(this).closest(".row").remove();
        subCounter--;
    });
    // Sub-Dropdown section end //
    
    // Has attribute NO start //
    $(document).on('change', '.has_attribute', function(){
        if ($('.has_attribute:checked').val() == 'Y') {
            $('.has_attribute_show').slideDown();
            $('.no_attribute_show').slideUp();
            $("input[id*=attr_price]").attr("required",true);
            $("input[id*=attr_title]").attr("required",true);
            
            $("#price").attr("required",false);

            $("#show_ingredients").prop('checked', false);
            $("#show_ingredients").attr('readonly', true);
            $("#show_ingredients").attr('disabled', 'disabled');
        } else {
            $("#price").attr("required",true);

            $("input[id*=attr_price]").attr("required",false);
            $("input[id*=attr_title]").attr("required",false);
            $('.has_attribute_show').slideUp();
            $('.no_attribute_show').slideDown();

            $("#show_ingredients").removeAttr('readonly');
            $("#show_ingredients").removeAttr('disabled');
        }
    });
    // Has attribute NO end //

    // Is menu NO start //
    $('#is_menu').click(function(){
        if ($(this).prop("checked") == true) {
            $('#attr_2').trigger('click');
            $("#attr_1").attr('disabled', 'disabled');
            $("#show_ingredients").prop("checked", false);
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", true);
            $('.is_menu_show').slideDown();
            $(document).find('.main-addon-selection').prop('required',true);
        }
        else if ($(this).prop("checked") == false) {
            $("#attr_1").removeAttr('disabled');
            $('#attr_1').trigger('click');
            $("#show_ingredients").attr('disabled', 'disabled');
            $('.addMenuRequired').prop("required", false);
            $('.is_menu_show').slideUp();
            $(document).find('.main-addon-selection').prop('required',false);
        }
    });
    // Is menu NO end //

    // Has attribute no first time start //
    //$('#attr_2').trigger('click');
    // Has attribute no first time end //
});


resetMainaddonvalue();

/*sp2*/
function setKeyForManiAddon(){
    // var indx=0;
    // $(document).find('.main-addon-selection').each(function(){
    //       $(this).attr('data-index',indx);
    //       indx++;
    // }) 
   // alert();
}
setKeyForManiAddon();

// $(document).on('click','.main-addon-selection',function(){ 

//     if($(this).closest('.mainrow-addons').find('.edit-drop-id').length){

//         alert();
//     }

// })

localStorage.clear();
$(document).on('change','.main-addon-selection',function(){ 
            var thisvals=$(this).val();
            //var subblckid=$(this).closest('.mainrow-addons').data('id');
            var subblckid=$(this).attr('data-index');
            var enmain=$(this).find('option:selected').data('en');
            var demain=$(this).find('option:selected').data('de');

            var addon_id=$(this).attr('data-addon_id');
            var menu_id=$(this).attr('data-menu_id');
            

            $(this).closest('.mainrow-addons').find('.en_main-title input').val(enmain);
            $(this).closest('.mainrow-addons').find('.de_main-title input').val(demain);

            var subCols='';
            var delete_id=[];
            $(this).closest('.mainrow-addons').find('.edit-drop-id').each(function(){    
                  delete_id.push($(this).val());            
                  var oldhtml=$(this).closest('.mainrow-addons').find('.addSubDropdownField_'+subblckid).html();
                  if(localStorage.getItem('old_edit_id'+subblckid)!=''){ 
                       localStorage.setItem('old_edit_id'+subblckid,oldhtml);
                  }                
            })
           
            if(typeof addon_id !== 'undefined' && thisvals==addon_id){
                if(localStorage.getItem('old_edit_id'+subblckid)){
                    subCols=localStorage.getItem('old_edit_id'+subblckid);
                }
            }else{

            
           // if($(document).find("input[name='dropdown['"+subblckid+"'][val_id][]']").length)

         //  edit-drop-id
            $(document).find('.allsub-menus option').each(function(){ 
                var name=$(this).text();
                var envalue=$(this).data('en');
                var idval=$(this).data('id');
                var price=$(this).data('price');
                var devalue=$(this).data('de');
                if(thisvals && thisvals==$(this).data('parent')){
                        //var newSubRow = $('<div class="row margin0" style="margin-top: 10px;">');
                        subCols += '<div class="row margin0" style="margin-bottom:10px">';
                        if(delete_id.length){
                            delete_id=String(delete_id);
                            subCols += '<input class="" type="hidden"  value="'+delete_id+'" name="dropdown['+subblckid+'][delete_ids][]">';
                            subCols += '<input class="" type="hidden"  value="'+menu_id+'" name="dropdown['+subblckid+'][delete_menu_ids][]">';
                        }
                        subCols += '<div class="col-md-2 checkbox-sub-div"><input class="checkbox-sub-value" type="hidden"  value="1" name="dropdown['+subblckid+'][value_sub_addon_status][]"><input class="" type="hidden"  value="" name="dropdown['+subblckid+'][val_id][]"><input class="" type="hidden"  value="'+idval+'" name="dropdown['+subblckid+'][value_sub_addon_id][]"><input class="checkbox-sub" type="checkbox" checked> '+name+'</div>';
                        subCols += '<div class="col-md-3">';
                        subCols += '<input class="form-control addMenuRequired" value="'+envalue+'" placeholder="" name="dropdown['+subblckid+'][val_en][]" type="text">';
                        subCols += '</div>';
                        subCols += '<div class="col-md-3">';
                        subCols += '<input class="form-control addMenuRequired" value="'+devalue+'" placeholder="" name="dropdown['+subblckid+'][val_de][]" type="text">';
                        subCols += '</div>';
                        subCols += '<div class="col-md-2">';
                        subCols += '<div class="input-group">';
                        subCols += '<div class="input-group-addon">CHF</div>';
                        subCols += '<input class="form-control addMenuRequired subDropDownPrice_'+subblckid+'" placeholder="" name="dropdown['+subblckid+'][val_price][]" min="0" value="'+price+'" type="number">';
                        subCols += '</div>';
                        subCols += '</div>';
                        //subCols += '<div class="col-md-2"><a class="btn btn-danger deleteSubDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
                        subCols += '</div>'; 
                }  
            }) 
        }
            $(this).closest('.mainrow-addons').find('.addSubDropdownField_'+subblckid).html(subCols);
            $(this).closest('.mainrow-addons').find('.without-selection-hide').addClass('hide');
            if(thisvals){
                $(this).closest('.mainrow-addons').find('.without-selection-hide').removeClass('hide');
            }
           resetMainaddonvalue();

           $(document).find('.collapse-type-body').hide();
          // $(document).find('.collapse-type-body').slideUp();           
           //$(this).closest('.mainrow-addons').find('.collapse-type-body').show();
           if($(this).val()){
              $(this).closest('.mainrow-addons').find('.collapse-type-body').slideDown();
           }
           resetMainaddonvalue();
})


function resetMainaddonvalue(){
    $(document).find('.main-addon-selection').find('option').removeClass('hide-div');
    $(document).find('.main-addon-selection').each(function(){

            //remove same value from another dropdown
            var values=[];
            $(document).find('.main-addon-selection').each(function(){
                if($(this).val()){
                    values.push($(this).val());
                }
            })

            console.log(values)
        
            $(document).find('.main-addon-selection').each(function(){              
                for(k=0; k<values.length;k++){
                        if(values[k]!=$(this).val()){
                            $(this).find('option[data-id="'+values[k]+'"]').addClass('hide-div');
                        }
                }
            })
    })

    $(document).find('.mainrow-addons').each(function(){
        $(this).find('.plus-minus-btn').removeClass('fa-angle-down');
        $(this).find('.plus-minus-btn').addClass('fa-angle-up');
        //console.log($(this).find('.menu_dropdown_option').css('display'));
        if($(this).find('.collapse-type-body').css('display')=='none' || $(this).find('.menu_dropdown_option').css('display')=='none'){
            $(this).find('.plus-minus-btn').removeClass('fa-angle-up');
            $(this).find('.plus-minus-btn').addClass('fa-angle-down');
        }
    })

}

$(document).on('click','.checkbox-sub',function(){
    var values=0;
    if($(this).prop('checked')==true){
        values=1;      
    }
    $(this).closest('.checkbox-sub-div').find('.checkbox-sub-value').val(values);
})

/**
 * Set Arrow on click
 */
$(document).on('click','.fa-angle-down',function(){
        $(this).closest('.mainrow-addons').find('.collapse-type-body').hide();
        $(this).closest('.mainrow-addons').find('.collapse-type-body').slideDown();
        setArrowSymbol();
})
/**
 * Set Arrow Set Arrow on click
 */
$(document).on('click','.fa-angle-up',function(){
      $(this).closest('.mainrow-addons').find('.collapse-type-body').slideUp();
      setArrowSymbol();
})
/**
 * Set Arrow
 */
function setArrowSymbol(){
    setTimeout(function() {
        $(document).find('.mainrow-addons').each(function(){
                $(this).find('.plus-minus-btn').removeClass('fa-angle-down');
                $(this).find('.plus-minus-btn').addClass('fa-angle-up');
                 console.log($(this).find('.menu_dropdown_option').hasClass('hide'));
                if($(this).find('.collapse-type-body').css('display')=='none' || $(this).find('.menu_dropdown_option').css('display')=='none'){
                    $(this).find('.plus-minus-btn').removeClass('fa-angle-up');
                    $(this).find('.plus-minus-btn').addClass('fa-angle-down');
                }
        })
    }, 500);
}


@if ($details->is_menu == 'Y')
$(document).ready(function(){
    $('#is_menu').click();
})
$('#price').prop('required',true);
@endif


@if ($details->has_attribute == 'Y')
    $('#show_ingredients').attr('readonly',true);
    $("#show_ingredients").attr('disabled', 'disabled');
@else
    $('#show_ingredients').removeAttr('readonly');
    $("#show_ingredients").removeAttr('disabled');
@endif


$(document).on('click', '.delete_dropdown_title', function() {
    //alert();
        var dropdowntitleId = $(this).data('dropdowntitle');
        var productId = '{{$details["id"]}}';

        Swal.fire({
			// title: '{{trans("custom_admin.message_warning")}}!',
            text: '{{trans("custom_admin.lab_want_delete")}}',
            icon: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            cancelButtonText: '{{trans("custom_admin.btn_cancel")}}',
            confirmButtonText: '{{trans("custom_admin.lab_yes")}}',
		}).then((result) => {
			if (result.value) {
				var deleteProductDropdowTitleUrl = '{{ route("admin.".\App::getLocale().".product.delete-dropdown-title") }}';
                $('#whole-area').show();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: deleteProductDropdowTitleUrl,
                    method: 'POST',
                    data: {
                        'dropdowntitle_id': dropdowntitleId,
                        'product_id': productId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        $('#whole-area').hide(); // Showing loader
                        
                        if(response.has_error == 0) {
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_success')}}",
                                text: response.message,
                                icon: "success",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            }).then((result) => {
                                window.location.reload();  
                            });                            
                        } else{
                            Swal.fire({
                                // title: "{{trans('custom_admin.message_error')}}",
                                text: response.message,
                                icon: "error",
                                showCancelButton: false,
                                // confirmButtonColor: '#d6f55b',
                                confirmButtonText: "Ok",
                                cancelButtonText: "",
                                closeOnConfirm: true,
                                closeOnCancel: false
                            });
                        }
                    }
                });
			}
		});
    });

    $(function () {
        
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_de');

    });


</script> 

@endif
@endsection
