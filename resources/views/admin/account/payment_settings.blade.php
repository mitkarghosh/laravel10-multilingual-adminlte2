@extends('admin.layouts.app', ['title' => $data['panel_title']])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $data['page_title'] }}
    </h1>
    <ol class="breadcrumb">
        <li><a><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $data['page_title'] }}</li>
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
                                    'route' => ['admin.'.\App::getLocale().'.payment-settings'],
                                    'name'  => 'updateSiteSettingsForm',
                                    'id'    => 'updateSiteSettingsForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
               <div class="box-body">
                  <input type="hidden" name="delivery[id][]" readonly="" class="form-control" value="1">
                  <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="FirstName">@lang('custom_admin.lab_gateway_select')<span class="red_star">*</span></label>
                              <select class="form-control select-gatway" name="gateway_type" placeholder="" required="required" readonly="">
                                  <option value="">@lang('custom_admin.lab_gateway_select')</option>
                                  <option value="stripe">@lang('custom_admin.lab_stripe')</option>
                                  <option value="payrexx">@lang('custom_admin.lab_payrexx')</option>
                              </select>
                           </div>
                        </div>
                    <div class="row stripe-col all-gateway hide-div">  
                        <div class="col-md-2 stripe-col all-gateway hide-div">
                                  <label for="IsShopClose">@lang('custom_admin.lab_status')</label>
                                  <div class="form-group admin-swicth">
                                        <label class="switch-new">
                                              @php $stripe_method=!empty($data['payment_setting'])?$data['payment_setting']->stripe_method:'' @endphp
                                              <input  type="checkbox" name="is_stripe_close" @if($stripe_method=='Y') checked  @endif class="is_stripe_close admin_switch" id="is_shop_close" value="Y">
                                              <span class="slider round"></span>
                                        </label>
                                  </div>
                          </div>

                        <div class="col-md-3 stripe-col all-gateway hide-div">
                            <div class="form-group">
                                  @php $stripe_publish_key=!empty($data['payment_setting'])?$data['payment_setting']->stripe_publish_key:'' @endphp
                                  <label for="IsShopClose">@lang('custom_admin.lab_stripe_publish_key') <span class="red_star">*</span></label>
                                  <input type="text" class="form-control" value="{{ $stripe_publish_key }}" name="stripe_publish_key" required>
                            </div>
                        </div>
                        <div class="col-md-3 stripe-col all-gateway hide-div">
                                <div class="form-group">
                                      @php $stripe_secret_key=!empty($data['payment_setting'])?$data['payment_setting']->stripe_secret_key:'' @endphp
                                      <label for="IsShopClose">@lang('custom_admin.lab_stripe_secrat_key') <span class="red_star">*</span></label>
                                      <input type="text" class="form-control" value="{{ $stripe_secret_key }}" name="stripe_secret_key" required>
                                  </div>
                        </div>
                    </div>

                      <div class="row stripe-col all-gateway hide-div" style="margin-left: 0px;">
                            <div class="col-md-3"></div>
                            <div class="col-md-2 stripe-col all-gateway hide-div">
                                    <label for="IsShopClose">@lang('custom_admin.label_new_card_payment')</label>
                                    <div class="form-group admin-swicth">
                                          <label class="switch-new">
                                                @php $is_stripe_fee=!empty($data['payment_setting'])?$data['payment_setting']->is_stripe_fee:'' @endphp
                                                <input  type="checkbox" name="is_stripe_fee" @if($is_stripe_fee=='Y') checked  @endif class="is_stripe_fee admin_switch" id="is_stripe_fee" value="Y">
                                                <span class="slider round"></span>
                                          </label>
                                    </div>
                            </div>
                            <div class="col-md-3 stripe-col all-gateway hide-div">
                              <div class="form-group">
                                    @php $stripe_fee_type=!empty($data['payment_setting'])?$data['payment_setting']->stripe_fee_type:'' @endphp
                                    {{--<label for="IsShopClose">@lang('custom_admin.lab_fee_type')</label>
                                    <select type="text" class="form-control" name="stripe_fee_type">
                                        <option value="F" @if($stripe_fee_type=='F') selected @endif>@lang('custom_admin.lab_flat')</option>
                                        <option value="P" @if($stripe_fee_type=='P') selected @endif>@lang('custom_admin.lab_percent')</option>
                                    </select>
                                    --}}
                                    <label for="IsShopClose">@lang('custom_admin.lab_amount') (@lang('custom_admin.lab_percent'))</label>
                                    <div class="input-group">
                                        <div class="input-group-addon payrexx_type_label1">
                                            %
                                        </div>
                                        @php $stripe_fee_amount_per=!empty($data['payment_setting'])?$data['payment_setting']->stripe_fee_amount_per:'0' @endphp
                                        <input id="amounts-1" min="0" placeholder="" class="form-control numberonly"  value="{{$stripe_fee_amount_per}}"  name="stripe_fee_amount_per" type="text">
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3 stripe-col all-gateway hide-div">
                               <div class="form-group">                                    
                                    <label for="Amount">@lang('custom_admin.lab_amount') (@lang('custom_admin.lab_flat'))</label>
                                    <div class="input-group">
                                        <div class="input-group-addon stripe_type_label1">
                                            CHF
                                        </div>
                                        @php $stripe_fee_amount=!empty($data['payment_setting'])?$data['payment_setting']->stripe_fee_amount:'0' @endphp
                                        <input id="amount-2" min="0" placeholder="" class="form-control numberonly"  value="{{$stripe_fee_amount}}"  name="stripe_fee_amount" type="text">
                                    </div>
                                </div>
                            </div>
                         </div>
                      <div class="row payrexx-col all-gateway hide-div"  style="margin-left: 0px;">  
                        <div class="col-md-2 payrexx-col all-gateway hide-div">
                                 <label for="IsShopClose">@lang('custom_admin.lab_status')</label>
                                 <div class="form-group admin-swicth">
                                       <label class="switch-new">
                                             @php $stripe_method=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_method:'' @endphp
                                             <input  type="checkbox" name="is_payrexx_close" @if($stripe_method=='Y') checked  @endif class="is_stripe_close admin_switch" id="is_shop_close" value="Y">
                                             <span class="slider round"></span>
                                       </label>
                                 </div>
                         </div> 
                        <div class="col-md-3 payrexx-col all-gateway hide-div" style="margin-left: 3px;">
                                 <div class="form-group">
                                    @php $payrexx_instance=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_instance:'' @endphp
                                    <label for="IsShopClose">@lang('custom_admin.label_instance') <span class="red_star">*</span></label>
                                      <input type="text" class="form-control" value="{{ $payrexx_instance }}" name="payrexx_instance" required>
                                 </div>
                        </div>
                        <div class="col-md-3 payrexx-col all-gateway hide-div" style="margin-left: 3px;">
                                 <div class="form-group">
                                    @php $payrexx_secret_key=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_secret_key:'' @endphp
                                    <label for="IsShopClose">@lang('custom_admin.label_secret_key') <span class="red_star">*</span></label>
                                      <input type="text" class="form-control" value="{{ $payrexx_secret_key }}" name="payrexx_secret_key" required>
                                 </div>
                        </div>
                      </div>    

                        <div class="row payrexx-col all-gateway hide-div" style="margin-left: 0px;">
                            <div class="col-md-3"></div>
                            <div class="col-md-2 payrexx-col all-gateway hide-div">
                                    <label for="IsShopClose">@lang('custom_admin.label_new_card_payment')</label>
                                    <div class="form-group admin-swicth">
                                          <label class="switch-new">
                                                @php $is_payrexx_fee=!empty($data['payment_setting'])?$data['payment_setting']->is_payrexx_fee:'' @endphp
                                                <input  type="checkbox" name="is_payrexx_fee" @if($is_payrexx_fee=='Y') checked  @endif class="is_payrexx_fee admin_switch" id="is_payrexx_fee" value="Y">
                                                <span class="slider round"></span>
                                          </label>
                                    </div>
                            </div>
                            <div class="col-md-3 payrexx-col all-gateway hide-div">
                              <div class="form-group">
                                    @php $payrexx_fee_type=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_fee_type:'' @endphp
                                   {{-- <label for="IsShopClose">@lang('custom_admin.lab_fee_type')</label>
                                    <select type="text" class="form-control" name="payrexx_fee_type">
                                        <option value="F" @if($payrexx_fee_type=='F') selected @endif>@lang('custom_admin.lab_flat')</option>
                                        <option value="P" @if($payrexx_fee_type=='P') selected @endif>@lang('custom_admin.lab_percent')</option>
                                    </select>
                                    --}}
                                    <label for="Amount">@lang('custom_admin.lab_amount') (@lang('custom_admin.lab_percent'))</label>
                                    <div class="input-group">
                                        <div class="input-group-addon payrexx_type_label1">
                                            %
                                        </div>
                                        @php $payrexx_fee_amount_per=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_fee_amount_per:'0' @endphp
                                        <input id="amounts-3" min="0" placeholder="" class="form-control numberonly"  value="{{$payrexx_fee_amount_per}}"  name="payrexx_fee_amount_per" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 payrexx-col all-gateway hide-div">
                               <div class="form-group">                                    
                                    <label for="Amount">@lang('custom_admin.lab_amount') (@lang('custom_admin.lab_flat'))</label>
                                    <div class="input-group">
                                        <div class="input-group-addon payrexx_type_label1">
                                          CHF
                                        </div>
                                        @php $payrexx_fee_amount=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_fee_amount:'0' @endphp
                                        <input id="amounts-4" min="0" placeholder="" class="form-control numberonly"  value="{{$payrexx_fee_amount}}"  name="payrexx_fee_amount" type="text">
                                    </div>
                                </div>
                            </div>
                         </div>
                        
                        
 


                        <div class="col-md-3 cash-col all-gateway hide-div">
                              <label for="FirstName">@lang('custom_admin.lab_status')</label>
                              <div class="form-group admin-swicth">
                                    <label class="switch-new">
                                        @php $payrexx_method=!empty($data['payment_setting'])?$data['payment_setting']->cash_method:'' @endphp
                                        <input  type="checkbox" name="is_cash_close" @if($payrexx_method=='Y') checked  @endif class="is_payrexx_close admin_switch" id="is_payrexx_close" value="Y">
                                        <span class="slider round"></span>
                                    </label>   
                              </div>
                        </div>

                        <div class="col-md-3 door-col all-gateway hide-div">
                              <label for="FirstName">@lang('custom_admin.lab_status')</label>
                              <div class="form-group admin-swicth">
                                     <label class="switch-new">
                                        @php $doorpayment=!empty($data['payment_setting'])?$data['payment_setting']->door_method:'' @endphp
                                        <input  type="checkbox" name="is_door_close" @if($doorpayment=='Y') checked  @endif class="is_door_close admin_switch" id="is_shop_close" value="Y">
                                        <span class="slider round"></span>
                                    </label>  
                              </div>
                        </div>

                  </div>
              
               </div>
               <div class="box-footer">
                  <div class="row">
                     <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_update')</button>
                        <a href="" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                     </div>
                  </div>
               </div>
            </form> 
         </div>
      </div>
   </div>
</section>



<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                   <h3 class="box-title">@lang('custom_admin.lab_payment_options')</h3>
                </div>
                   <div class="box-body">
                    <table class="table table-bordered">
                      <tbody>
                          <tr>
                         
                          <th>@lang('custom_admin.lab_payment_options')</th>
                          <th width="100">@lang('custom_admin.lab_status')</th>
                          <th class="action_width text_align_center">@lang('custom_admin.lab_action')</th>
                          </tr>
                    <tr>
                        <!-- <td class="srno">2</td> -->
                        <td>@lang('custom_admin.lab_payment_cod') </td>
                        <td>
                            @if($data['payment_setting']->cash_method == 'Y')
                              <span class="label  label-success ">
                                  <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'cash']) }}" title="Status">
                                  @lang('custom_admin.lab_active')                                
                                  </a>
                              </span>                   
                            @else
                              <span class="label  label-danger ">
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'cash']) }}" title="Status">
                                @lang('custom_admin.lab_inactive')                                
                                </a>
                              </span>      
                            @endif
                        </td>   
                        <td>
                        <a href="javascript:void()" onclick="editPayment('cod')" title="Edit"  class="btn btn-info btn-sm disabled">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                           </a>
                           &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.delete-payment-gateway', [$data['payment_setting']->id,'cash']) }}" class="btn btn-danger btn-sm disabled"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                        </td>
                      </tr> 
                   
                      <tr>
     
                        <td>@lang('custom.label_card_on_door') </td>
                        <td>
                            @if($data['payment_setting']->door_method == 'Y')
                              <span class="label  label-success ">
                                  <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'door']) }}" title="Status">
                                  @lang('custom_admin.lab_active')                                
                                  </a>
                              </span>                   
                            @else
                              <span class="label  label-danger ">
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'door']) }}" title="Status">
                                @lang('custom_admin.lab_inactive')                                
                                </a>
                              </span>      
                            @endif
                        </td>   
                        <td>
                           <a href="javascript:void()" onclick="editPayment('door')" title="" class="btn btn-info btn-sm disabled">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                           </a>
                           &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.delete-payment-gateway', [$data['payment_setting']->id,'door']) }}" class="btn btn-danger btn-sm disabled"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                        </td>
                      </tr> 
                    

                   
                      
                  

                    @php $doorpayment=!empty($data['payment_setting'])?$data['payment_setting']->stripe_active:'' @endphp
                    @if($doorpayment)
                      <tr>
                        
                        <td>Online Gateway  @lang('custom_admin.lab_stripe') </td>
                        <td>
                            @if($data['payment_setting']->stripe_method == 'Y')
                              <span class="label  label-success ">
                                  <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'stripe']) }}" title="Status">
                                  @lang('custom_admin.lab_active')                                
                                  </a>
                              </span>                   
                            @else
                              <span class="label  label-danger ">
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_active')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'stripe']) }}" title="Status">
                                @lang('custom_admin.lab_inactive')                                
                                </a>
                              </span>      
                            @endif
                        </td>   
                        <td>
                        <a href="javascript:void()" onclick="editPayment('stripe')" title="Edit" class="btn btn-info btn-sm">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                           </a>
                           &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.delete-payment-gateway', [$data['payment_setting']->id,'stripe']) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                        </td>
                      </tr> 
                    @endif

                    @php $doorpayment=!empty($data['payment_setting'])?$data['payment_setting']->payrexx_active:'' @endphp
                    @if($doorpayment)
                      <tr>
                        
                        <td>Online Gateway @lang('custom_admin.lab_payrexx') </td>
                        <td>
                            @if($data['payment_setting']->payrexx_method == 'Y')
                              <span class="label  label-success ">
                                  <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_inactive')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'payrexx']) }}" title="Status">
                                  @lang('custom_admin.lab_active')                                
                                  </a>
                              </span>                   
                            @else
                              <span class="label  label-danger ">
                                <a class="color_white" href="javascript:void(0)" onclick="return sweetalertMessageRender(this,'@lang('custom_admin.lab_want_active')',  'warning', true)" data-href="{{ route('admin.'.\App::getLocale().'.change-payment-status', [$data['payment_setting']->id,'payrexx']) }}" title="Status">
                                @lang('custom_admin.lab_inactive')                                
                                </a>
                              </span>      
                            @endif
                        </td>   
                        <td>
                         <a href="javascript:void()" onclick="editPayment('payrexx')" title="Edit" class="btn btn-info btn-sm">
                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                           </a>
                           &nbsp;
                            <a onclick="return sweetalertMessageRender(this, '@lang('custom_admin.lab_want_delete')', 'error',  true)" href="javascript:void(0)" title="Delete" data-href="{{ route('admin.'.\App::getLocale().'.delete-payment-gateway', [$data['payment_setting']->id,'payrexx']) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i></a>         
                        </td>
                      </tr> 
                    @endif
                          
                       </tbody>
                    </table>
                </div>
               
            </div>
        </div>
    </div>
</section>
 
<!-- /.content -->
<script>

 /***
  * Hide show section
  */
 $(document).on('change','.select-gatway',function(){
     var thisvalues=$(this).val();
     $(document).find('.all-gateway').addClass('hide-div');
     //alert(thisvalues)
     if(thisvalues=='cod'){
         $(document).find('.cash-col').removeClass('hide-div');
     }
     if(thisvalues=='stripe'){
         $(document).find('.stripe-col').removeClass('hide-div');
     }
     if(thisvalues=='door'){
        $(document).find('.door-col').removeClass('hide-div');
     }
     if(thisvalues=='payrexx'){
         $(document).find('.payrexx-col').removeClass('hide-div');
     } 
 })
  /**
  * Set sr no
  */
 var inno=1;
 $('.srno').each(function(){
    $(this).text(inno);
    inno++;
 })
 /**
  * Edit Gateway
  */
 function editPayment(type){
    $(document).find('.alert-dismissable').remove();
    $('.select-gatway').val(type).trigger('change');
    var body = $("html, body");
    body.stop().animate({scrollTop:0}, 500, 'swing', function() {  
    }); 
 }
/**
 * Check stripe fee
 */
 @if($is_stripe_fee!='Y')
   $('input[name="stripe_fee_amount_per"]').prop('disabled',true);
   $('input[name="stripe_fee_amount"]').prop('disabled',true);
   $('input[name="stripe_fee_amount_per"]').prop('required',false);
   $('input[name="stripe_fee_amount"]').prop('required',false);
 @else
 $('input[name="stripe_fee_amount_per"]').prop('disabled',false);
 $('input[name="stripe_fee_amount"]').prop('disabled',false);
 $('input[name="stripe_fee_amount_per"]').prop('required',true);
   $('input[name="stripe_fee_amount"]').prop('required',true);
 @endif
/**
 * Check stripe fee true or not
 */
$(document).on('click','.is_stripe_fee',function(){
    $('input[name="stripe_fee_amount_per"]').prop('disabled',true);
    $('input[name="stripe_fee_amount"]').prop('disabled',true);
    $('input[name="stripe_fee_amount_per"]').prop('required',false);
    $('input[name="stripe_fee_amount"]').prop('required',false);
   if($(this).prop('checked')==true){
      $('input[name="stripe_fee_amount_per"]').prop('disabled',false);
      $('input[name="stripe_fee_amount"]').prop('disabled',false);
      $('input[name="stripe_fee_amount_per"]').prop('required',true);
      $('input[name="stripe_fee_amount"]').prop('required',true);
   }
})
/**
 * Check payrexx fee
 */
@if($is_payrexx_fee!='Y')
      $('input[name="payrexx_fee_amount_per"]').prop('disabled',true);
      $('input[name="payrexx_fee_amount"]').prop('disabled',true);
      $('input[name="payrexx_fee_amount_per"]').prop('required',false);
      $('input[name="payrexx_fee_amount"]').prop('required',false);
 @else
 $('input[name="payrexx_fee_amount_per"]').prop('disabled',false);
 $('input[name="payrexx_fee_amount"]').prop('disabled',false); 
 $('input[name="payrexx_fee_amount_per"]').prop('required',true);
  $('input[name="payrexx_fee_amount"]').prop('required',true);
 @endif
/**
 * Check payrexx true or not
 */
$(document).on('click','.is_payrexx_fee',function(){
    $('input[name="payrexx_fee_amount_per"]').prop('disabled',true);
    $('input[name="payrexx_fee_amount"]').prop('disabled',true);
    $('input[name="payrexx_fee_amount_per"]').prop('required',false);
    $('input[name="payrexx_fee_amount"]').prop('required',false);
   if($(this).prop('checked')==true){
      $('input[name="payrexx_fee_amount_per"]').prop('disabled',false);
      $('input[name="payrexx_fee_amount"]').prop('disabled',false);
      $('input[name="payrexx_fee_amount_per"]').prop('required',true);
      $('input[name="payrexx_fee_amount"]').prop('required',true);
    }
})
/**
 * amount label change on change fee type
 */
function stripeFeeTypeLabel(){
    var thisvalue=$('select[name="stripe_fee_type"]').val();
      var siteLang    = $('#website_lang').val();
      var label='';
      if(siteLang=='en'){
        label='%';
        if(thisvalue=='F'){
           label='CHF';
        }
      }else{
        label='%';
        if(thisvalue=='F'){
           label='CHF';
        }
      }
      $('.stripe_type_label').text(label);
}
function payrexxFeeTypeLabel(){
      var thisvalue=$('select[name="payrexx_fee_type"]').val();
      var siteLang    = $('#website_lang').val();
      var label='';
      if(siteLang=='en'){
        label='%';
        if(thisvalue=='F'){
           label='CHF';
        }
      }else{
        label='%';
        if(thisvalue=='F'){
           label='CHF';
        }
      }
      $('.payrexx_type_label').text(label);
}
$('select[name="stripe_fee_type"]').change(function(){
    stripeFeeTypeLabel();
})

/**
 * amount label change on change fee type
 */
$('select[name="payrexx_fee_type"]').change(function(){
    payrexxFeeTypeLabel();
})
/**@
 * On page reload set label for fee type
 */
stripeFeeTypeLabel();
payrexxFeeTypeLabel();

</script>


@endsection