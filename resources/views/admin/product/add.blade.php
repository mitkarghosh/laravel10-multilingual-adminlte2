@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

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
                                    'route' => ['admin.'.\App::getLocale().'.product.addsubmit'],
                                    'name'  => 'addProductForm',
                                    'id'    => 'addProductForm',
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
                                    <label for="Name">@lang('custom_admin.lab_description')</label>
                                    {{ Form::textarea('description_en', null, array(
                                                                'id'=>'description_en',
                                                                'class' => 'form-control' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="NameArabic">@lang('custom_admin.lab_description_dutch')</label>
                                    {{ Form::textarea('description_de', null, array(
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
                                        <option value="{{$keyCategory}}" @if($keyCategory == old('category_id') ) selected="selected" @endif>{{$valCategory}}</option>
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_is_menu')</label>
                                    <div>
                                        <label class="form-check-label">
                                            {!! Form::checkbox('is_menu', 'Y', null, array('id' => 'is_menu', 'class'=>'form-check-input', 'autocomplete' => 'off')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
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
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'Y', null, array('checked', 'class'=>'form-check-input has_attribute', 'id' => 'attr_1')) !!}
                                                    &nbsp;@lang('custom_admin.lab_yes')
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-radio">
                                                <label class="form-check-label">
                                                    {!! Form::radio('has_attribute', 'N', null, array('class'=>'form-check-input has_attribute', 'id' => 'attr_2')) !!}
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
                                        <label class="form-check-label">
                                            {!! Form::checkbox('show_ingredients', 'Y', null, array('id' => 'show_ingredients', 'class'=>'form-check-input')) !!}
                                            &nbsp;@lang('custom_admin.lab_yes')
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group has_attribute_show">
                                    <label for="title">@lang('custom_admin.lab_create_attribute')<span class="red_star">*</span></label>
                                    <div class="addField">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="title">@lang('custom_admin.lab_english')</label>
                                                {!! Form::text('attr_title_en[0]', null, array('required', 'class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_en0')) !!}
                                            </div>
                                            <div class="col-md-4">
                                                <label for="title">@lang('custom_admin.lab_dutch')</label>
                                                {!! Form::text('attr_title_de[0]', null, array('required', 'class'=>'form-control', 'placeholder' => '', 'id' => 'attr_title_de0')) !!}
                                            </div>
                                            <div class="col-md-2">
                                                <label for="title">&nbsp;</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        CHF
                                                    </div>
                                                    {!! Form::number('attr_price', null, array('required','class'=>'form-control', 'placeholder' => '', 'name'=>'attr_price[]', 'id' => 'attr_price0', 'min' => 0)) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="title">&nbsp;</label><br />
                                                <button class="btn btn-success add-more" id="addrow" type="button"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group no_attribute_show" style="display: none;">
                                    <label for="title">@lang('custom_admin.lab_price')<span class="red_star">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::number('price', null, array(
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
                                        
                                        <div class="row maindropdown_0 mainrow-addons" data-id="0">                                            
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="collapse-type-heading">
                                                        <div class="col-md-4">
                                                            <select required class="form-control main-addon-selection" data-index="0" name="dropdown[0][main_addon]">
                                                                <option value="">@lang('custom_admin.lab_select')</option>
                                                                @foreach($addon_list as $list)                                                                    
                                                                    <option data-id="{{$list->id}}" value="{{$list->id}}" data-de="{{$list->de_title }}" data-en="{{$list->en_title }}"> @if(App::getLocale() == 'de')  {{$list->de_title }} @else {{$list->en_title}}  @endif </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button class="btn btn-success add-more float-right" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                                        <i class="plus-minus-btn fa fa-angle-down" aria-hidden="true"></i>
                                                        <!-- <i class="plus-minus-btn fa fa-angle-up" aria-hidden="true"></i> -->
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="collapse-type-body ">
                                                    <div class="row">
                                                        {{-- Main Grouping start --}}
                                                        <div class="col-md-10 without-selection-hide menu_dropdown_option hide">
                                                            <div class="row hide-1">
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
                                                            <button class="btn btn-success add-more hide" id="addDropdownRow" type="button"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                        {{-- Main Grouping end --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">                            
                                <div class="form-group">                                    
                                    <label for="categories">@lang('custom_admin.lab_tag')(s)</label>
                                    <select name="tags[]" id="tags" multiple="multiple" class="form-control select2">
                                    @foreach ($tagList as $keyTag => $valTag)
                                        <option value="{{$keyTag}}">{{$valTag}}</option>
                                    @endforeach
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
    CKEDITOR.replace('description_en');
    CKEDITOR.replace('description_de');

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
    var mainBlockCounter = subBlockCounter = 0;
    $("#addDropdownRow").on("click", function () {

        var totdaladdon=Number($(document).find('.main-addon-selection').length);
        
        var totalitem=Number($(document).find('.total-menu-item').data('count'));
 

        if (totdaladdon<totalitem) {
            mainBlockCounter++;
            subBlockCounter++;
            var cols = subcols = '';
            var deleticn = '<a class="btn btn-danger deleteDropdownRow delete-dropdown-products"  href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a><i class="plus-minus-btn fa fa-angle-up" aria-hidden="true"></i>';    
            var allmainoption="<div class='collapse-type-heading'><div class='col-md-12'><div class='row' style='margin-bottom:10px'><div class='col-md-4'><select required data-index='"+mainBlockCounter+"' name='dropdown["+mainBlockCounter+"][main_addon]' class='form-control main-addon-selection'>{!! $addon_list_dropdown !!}</select></div><div class='col-md-6'></div><div class='col-md-2 text-right pr-0'>"+deleticn+"</div>  </div></div></div>";

            var newDropdownRow = $('<div class="row mainrow-addons maindropdown_'+mainBlockCounter+'" data-id="'+mainBlockCounter+'" style="margin-top: 10px;">');
            cols += allmainoption;
            cols += '<div class="col-md-12"><div class="collapse-type-body "><div class="row"><div class="col-md-10 without-selection-hide hide menu_dropdown_option">';
            cols += '<div class="row hide-1"><div class="col-md-12">';
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
    // $(".row").on("click", ".deleteDropdownRow", function (event) {
    //     $(this).closest(".row").remove();
    //     mainBlockCounter--;
    // });
    // Dropdown section end //

    // Sub-Dropdown section start //
    // var subCounter = 0;
    // $(document).on("click", ".addSubDropdownrow", function () {
    //     subblckid = $(this).data('subblckid');
    //     subCounter++;
    //     var subCols = '';
    //     var newSubRow = $('<div class="row margin0" style="margin-top: 10px;">');
        
    //     //subCols += '<div class="row margin0">';
    //     subCols += '<div class="col-md-4">';
    //     subCols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+subblckid+'][val_en][]" type="text">';
    //     subCols += '</div>';
    //     subCols += '<div class="col-md-4">';
    //     subCols += '<input class="form-control addMenuRequired" placeholder="" name="dropdown['+subblckid+'][val_de][]" type="text">';
    //     subCols += '</div>';
    //     subCols += '<div class="col-md-2">';
    //     subCols += '<div class="input-group">';
    //     subCols += '<div class="input-group-addon">CHF</div>';
    //     subCols += '<input class="form-control addMenuRequired subDropDownPrice_'+subblckid+'" placeholder="" name="dropdown['+subblckid+'][val_price][]" min="0" value="0" type="number">';
    //     subCols += '</div>';
    //     subCols += '</div>';
    //     subCols += '<div class="col-md-2"><a class="btn btn-danger deleteSubDropdownRow" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
    //     //subCols += '</div>';

    //     newSubRow.append(subCols);
    //     $(".addSubDropdownField_"+subblckid).append(newSubRow);
    // });
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
    
    
     $('#is_menu').each(function(){
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
    $('#attr_2').trigger('click');
    // Has attribute no first time end //
});



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
$(document).on('change','.main-addon-selection',function(){ 
            var thisvals=$(this).val();
            //var subblckid=$(this).closest('.mainrow-addons').data('id');
            var enmain=$(this).find('option:selected').data('en');
            var demain=$(this).find('option:selected').data('de');
            var subblckid=$(this).attr('data-index');

            $(this).closest('.mainrow-addons').find('.en_main-title input').val(enmain);
            $(this).closest('.mainrow-addons').find('.de_main-title input').val(demain);

            var subCols='';
            $(document).find('.allsub-menus option').each(function(){ 
                var name=$(this).text();
                var envalue=$(this).data('en');
                var idval=$(this).data('id');
                var price=$(this).data('price');
                var devalue=$(this).data('de');
                if(thisvals && thisvals==$(this).data('parent')){
                        //var newSubRow = $('<div class="row margin0" style="margin-top: 10px;">');
                        subCols += '<div class="row margin0" style="margin-bottom:10px">';
                        subCols += '<div class="col-md-2 checkbox-sub-div"><input class="checkbox-sub-value" type="hidden"  value="1" name="dropdown['+subblckid+'][value_sub_addon_status][]"><input class="" type="hidden"  value="'+idval+'" name="dropdown['+subblckid+'][value_sub_addon_id][]"><input class="checkbox-sub" type="checkbox" checked> '+name+'</div>';
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
            $(this).closest('.mainrow-addons').find('.addSubDropdownField_'+subblckid).html(subCols);
            $(this).closest('.mainrow-addons').find('.without-selection-hide').addClass('hide');
            if(thisvals){
                $(this).closest('.mainrow-addons').find('.without-selection-hide').removeClass('hide');
            }
         
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
        
            $(document).find('.main-addon-selection').each(function(){              
                for(k=0; k<values.length;k++){
                        if(values[k]!=$(this).val()){
                            $(this).find('option[data-id="'+values[k]+'"]').addClass('hide-div');
                        }
                }
            })
            
           // $(this).closest('.collapse-type-heading').find('.plus-minus-btn')
            //collapse-type-body
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

</script>

@endsection
