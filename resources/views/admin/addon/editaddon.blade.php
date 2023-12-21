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
                                    <input type="hidden" value="{{ $addon_id }}" name="update_id">
                    <div class="box-body">
                    @php 
                      $k=0;
                      $p=0;
                    @endphp
                    @foreach($list as $addonlist)
                       @if($k==0)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title')<span class="red_star">*</span></label>
                                    <input id="english_title" value="{{ $addonlist->en_title }}" placeholder="@lang('custom_admin.lab_title')" class="form-control" required="required" name="english_title" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('custom_admin.lab_title_dutch')<span class="red_star">*</span></label>
                                    <input id="german_title" value="{{ $addonlist->de_title }}" placeholder="@lang('custom_admin.lab_title_dutch')" class="form-control" required="required" name="german_title" type="text">
                                </div>
                            </div>
                        </div>        
                         @else              
                        <div class="row new-row-added-for-addon">
                            <div class="col-md-3">
                                <div class="form-group">
                                @if($p==0)  <label for="title">@lang('custom_admin.lab_dropdown_value')<span class="red_star">*</span></label>
                                @endif      
                                    <input value="{{ $addonlist->id }}" name="sub_addon_id[]" type="hidden">
                                    <input value="{{ $addonlist->en_value }}" id="english_value" placeholder="@lang('custom_admin.lab_dropdown_value')" class="form-control" required="required" name="english_value[]" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                @if($p==0)  <label for="title">@lang('custom_admin.lab_dropdown_value_dutch')<span class="red_star">*</span></label>
                                @endif   
                                <input value="{{ $addonlist->de_value }}" id="value_german" placeholder="@lang('custom_admin.lab_dropdown_value_dutch')" class="form-control" required="required" name="value_german[]" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                @if($p==0)  <label for="title">@lang('custom_admin.lab_price')<span class="red_star">*</span></label> @endif
                                    <input value="{{ $addonlist->price }}" id="value_german" placeholder="@lang('custom_admin.lab_price')" class="form-control numberonly" required="required" name="price[]" type="text">
                                </div>
                            </div>
                            <div class="col-md-2">
                                 <div class="form-group admin-swicth">
                                 @if($p==0) <label for="title" style="width:100%">@lang('custom_admin.lab_status')<span class="red_star">&nbsp;</span></label> @endif
                                       <label class="switch-new">
                                             @php $stripe_method=!empty($addonlist->status)?1:0 @endphp
                                             <input type="hidden" value="{{$stripe_method}}" class="toggle_status" name="status[]">
                                             <input  type="checkbox"  @if($stripe_method==1) checked  @endif class="is_stripe_close admin_switch status-switch" id="is_shop_close" value="Y">
                                             <span class="slider round"></span>
                                       </label>
                                 </div>
                                 <!-- <div class="form-group admin-swicth">
                                    @if($p==0) <label for="title" style="width:100%">@lang('custom_admin.lab_status')<span class="red_star">&nbsp;</span></label> @endif
                                    <span class="label @if($addonlist->status == 1) label-success @else label-danger @endif">
                                    @if($addonlist->status == '1')
                                    <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.product.addon-change-status', [$addonlist->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_active')
                                    </a>
                                    @else
                                    <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning',  true)" data-href="{{ route('admin.'.\App::getLocale().'.product.addon-change-status', [$addonlist->id]) }}" title="@lang('custom_admin.lab_status')">
                                    @lang('custom_admin.lab_inactive')
                                    </a>
                                    @endif
                                    </span>
                                 </div>     -->
                            </div>
                            <div class="col-md-1" >
                                 <div class="form-group">
                                 @if($p==0)  <label style="width:100%">&nbsp;</label> @endif
                                    @if($p!=0)
                                    <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.product.delete-addon', [$addonlist->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                   
                                    @else
                                    <button class="btn btn-success add-more" id="addDropdownRowAddon" type="button"><i class="fa fa-plus"></i></button>
                                    @endif
                                  </div>
                             </div>
                        </div>
                        @php $p++ @endphp
                        @endif 
                       @php $k++ @endphp
                       @endforeach 
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
                       var label_du="@lang('custom_admin.lab_dropdown_value')";
                       var label_price="@lang('custom_admin.lab_price')";
                       mainBlockCounter++;
                        var myvar = '<div class="row new-row-added-for-addon">'+
                        '                            <div class="col-md-3">'+
                        '                                <div class="form-group">'+
                        '                                    <label for="title" class="hide">'+label_en+'<span class="red_star">*</span></label>'+
                        '                                    <input id="english_value-'+mainBlockCounter+'" placeholder="'+label_en+'" placeholder="" class="form-control english" required="required" name="english_value[]" type="text">'+
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
                        '                                    <input id="value_price-'+mainBlockCounter+'" placeholder="'+label_price+'"  placeholder="" class="form-control numberonly price" required="required" name="price[]" type="text">'+
                        '                                </div>'+
                        '                            </div>'+
                        '                            <div class="col-md-2"><div class="form-group admin-swicth"> <label class="switch-new"> <input type="hidden" name="status[]" value="1"> <input type="checkbox"  checked="" class="is_stripe_close admin_switch status-switch" id="is_shop_close" value="Y"> <span class="slider round"></span> </label> </div></div><div class="col-md-1">'+
                        '                                 <div class="form-group">'+
                        '                                    <label style="width:100%" class="hide"> </label>'+
                        '                                    <a class="btn btn-danger remove-addon-row-extra" data-dropdowntitle="106" href="javascript: void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>'+
                        '                                   '+
                        '                                  </div>'+
                        '                             </div>'+
                        '                        </div>';
	
              $('.new-addon-row').before(myvar);
              var k=0;
              $(document).find('.new-row-added-for-addon').each(function(){
                    $(this).find('.german').attr('name','value_german['+k+']');
                    $(this).find('.english').attr('name','english_value['+k+']');
                    $(this).find('.price').attr('name','price['+k+']');
                    $(this).find('.toggle_status').attr('name','status['+k+']');
                    
                    k++; 
              })
    });

   $(document).on('click','.status-switch',function(){
           var status=0;
           if($(this).prop('checked')==true){
               status=1;
           }
           $(this).closest('div').find('input[name="status[]"]').val(status);
   })

    $(document).on('click','.remove-addon-row-extra',function(){
       
        if($(this).closest('.new-row-added-for-addon').find('input[name="sub_addon_id[]"]').length<1){
                $(this).closest('.new-row-added-for-addon').remove();
        }
        var k=0;
        $(document).find('.new-row-added-for-addon').each(function(){
            $(this).find('.german').attr('name','value_german['+k+']');
            $(this).find('.english').attr('name','english_value['+k+']');
            $(this).find('.price').attr('name','price['+k+']');
            $(this).find('.toggle_status').attr('name','status['+k+']');
            k++; 
        })
    
    })
</script>        
@endsection