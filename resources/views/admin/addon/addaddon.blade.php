@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $page_title }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                                                    
    </div>                
    @include('admin.elements.notification')
                
                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.product.addon-submit'],
                                    'name'  => 'addAddonProductNew',
                                    'id'    => 'addAddonProductNew',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    <input id="english_title" placeholder="@lang('custom_admin.lab_title')" class="form-control" required="required" name="english_title" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title_dutch')<span class="red_star">*</span></label>
                                    <input id="german_title" placeholder="@lang('custom_admin.lab_title_dutch')" class="form-control" required="required" name="german_title" type="text">
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_dropdown_value')<span class="red_star">*</span></label>
                                    <input id="english_value" placeholder="@lang('custom_admin.lab_dropdown_value')" class="form-control" required="required" name="english_value[]" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_dropdown_value_dutch')<span class="red_star">*</span></label>
                                    <input id="value_german" placeholder="@lang('custom_admin.lab_dropdown_value_dutch')" class="form-control" required="required" name="value_german[]" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_price')<span class="red_star">*</span></label>
                                    <input id="value_german" placeholder="@lang('custom_admin.lab_price')" class="form-control numberonly" required="required" name="price[]" maxlength="6" type="text">
                                </div>
                            </div>
                            <div class="col-md-2" >
                                 <div class="form-group">
                                    <label style="width:100%">&nbsp;</label>
                                   
                                    <button class="btn btn-success add-more" id="addDropdownRowAddon" type="button"><i class="fa fa-plus"></i></button>
                                  </div>
                             </div>
                        </div>
                        <span class="new-addon-row"></span>
               
                    </div>                        
                    <div class="box-footer">
                        <div class="col-md-6 pl-0">
                            <button type="button" class="btn btn-primary add-addon-submit">@lang('custom_admin.btn_submit')</button>
                            <a href="{{ route('admin.'.\App::getLocale().'.product.addonlist') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                        </div>
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
<script>
    var mainBlockCounter=subBlockCounter=0;
     $("#addDropdownRowAddon").on("click", function () {
                       var label_en="@lang('custom_admin.lab_dropdown_value')";
                       var label_du="@lang('custom_admin.lab_dropdown_value_dutch')";
                       var label_price="@lang('custom_admin.lab_price')";
                       mainBlockCounter++;
                        var myvar = '<div class="row new-row-added-for-addon">'+
                        '                            <div class="col-md-3">'+
                        '                                <div class="form-group">'+
                        '                                    <label for="title" class="hide">'+label_en+'<span class="red_star">*</span></label>'+
                        '                                    <input id="english_value-'+mainBlockCounter+'" placeholder="'+label_en+'" class="form-control english" required="required" name="english_value[]" type="text">'+
                        '                                </div>'+
                        '                            </div>'+
                        '                            <div class="col-md-3">'+
                        '                                <div class="form-group">'+
                        '                                    <label for="title" class="hide">'+label_du+'<span class="red_star">*</span></label>'+
                        '                                    <input id="value_german-'+mainBlockCounter+'" placeholder="'+label_du+'" class="form-control german" required="required" name="value_german[]" type="text">'+
                        '                                </div>'+
                        '                            </div>'+
                        '                            <div class="col-md-3">'+
                        '                                <div class="form-group">'+
                        '                                    <label for="title" class="hide">'+label_price+'<span class="red_star">*</span></label>'+
                        '                                    <input id="value_price-'+mainBlockCounter+'" placeholder="'+label_price+'" maxlength="6" class="form-control numberonly price" required="required" name="price[]" type="text">'+
                        '                                </div>'+
                        '                            </div>'+
                        '                            <div class="col-md-2">'+
                        '                                 <div class="form-group" class="hide">'+
                        '                                    <label style="width:100%" class="hide"> </label>'+
                        '                                    <a class="btn btn-danger remove-addon-row-extra" data-dropdowntitle="106" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>'+
                        '                                   '+
                        '                                  </div>'+
                        '                             </div>'+
                        '                        </div>';
	
              $('.new-addon-row').before(myvar);
              var k=1;
              $(document).find('.new-row-added-for-addon').each(function(){
                    $(this).find('.german').attr('name','value_german['+k+']');
                    $(this).find('.english').attr('name','english_value['+k+']');
                    $(this).find('.price').attr('name','price['+k+']');
                    k++; 
              })
    });


    $(document).on('click','.remove-addon-row-extra',function(){
        if(confirm('Are you sure?')){
            $(this).closest('.new-row-added-for-addon').remove();
            var k=1;
            $(document).find('.new-row-added-for-addon').each(function(){
                $(this).find('.german').attr('name','value_german['+k+']');
                $(this).find('.english').attr('name','english_value['+k+']');
                $(this).find('.price').attr('name','price['+k+']');
                k++; 
            })
        }
    })
</script>        
@endsection