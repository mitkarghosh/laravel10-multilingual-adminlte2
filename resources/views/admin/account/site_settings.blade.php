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

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('custom_admin.lab_edit')</h3>
                </div>

                @include('admin.elements.notification')

                {{ Form::open(array(
		                            'method'=> 'POST',
		                            'class' => '',
                                    'route' => ['admin.'.\App::getLocale().'.site-settings'],
                                    'name'  => 'updateSiteSettingsForm',
                                    'id'    => 'updateSiteSettingsForm',
                                    'files' => true,
		                            'novalidate' => true)) }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.lab_from_email')<span class="red_star">*</span></label>
                                    {{ Form::text('from_email', $data['from_email'], array(
                                                                'id' => 'from_email',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="LastName">@lang('custom_admin.lab_to_email')<span class="red_star">*</span></label>
                                    {{ Form::text('to_email', $data['to_email'], array(
                                                                'id' => 'to_email',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_website_email')<span class="red_star"></span></label>
                                    {{ Form::text('website_title', $data['website_title'], array(
                                                                'id' => 'website_title',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_website_link')<span class="red_star"></span></label>
                                    {{ Form::text('website_link', $data['website_link'], array(
                                                                'id' => 'website_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.lab_global_logo')<span class="red_star"></span>  </label>
                                    <input type="file" class="form-control" name="logo" @if(empty($data['logo']))   @endif>
                                    <p><span>@lang('custom_admin.lab_file_dimension') 120px X 100px</span></p>
                                    <input type="hidden" value="{{ $data['logo'] }}" name="uploaded_logo">
                                    
                                        <div class="logo-wrap-div @if(empty($data['logo_url'])) hide @endif">
                                            <img src="{{ $data['logo_url'] }}" class="logo-site-setting"> 
                                            <a class="remove-image-logos" href="javascript:void(0)" style="display: inline;">×</a>
                                        </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.lab_global_header_picture')<span class="red_star">*</span></label>
                                    <input type="file" class="form-control" name="header_picture" @if(empty($data['header_picture']))   @endif>
                                    <p><span>@lang('custom_admin.lab_file_dimension') 1600px X 360px</span></p>
                                    <input type="hidden" value="{{ $data['header_picture'] }}" name="uploaded_header_picture"> 
                                    <div class="img-wrap-div @if(empty($data['header_picture'])) hide @endif">
                                        <img src="{{ $data['header_picture_url'] }}"  class="header_picture-site-setting"> 
                                        <a class="remove-image-logos" href="javascript:void(0)" style="display: inline;">×</a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.lab_global_logo')<span class="red_star"></span> (PNG)</label>
                                    <input type="file" class="form-control" name="logo_png" @if(empty($data['logo_png']))   @endif>
                                    <input type="hidden" value="{{ $data['logo_png'] }}" name="uploaded_png_logo">
                                    <p><span>@lang('custom_admin.lab_file_dimension') 240px X 240px</span></p>
                                   
                                    <div class="logo-wrap-div  @if(empty($data['png_logo_url'])) hide @endif">
                                        <img src="{{ $data['png_logo_url'] }}" class="logo-site-setting">
                                        <a class="remove-image-logos" href="javascript:void(0)" style="display: inline;">×</a>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.advertising_image_en')(en)<span class="red_star"></span>  </label>
                                    <input type="file" class="form-control" name="advertisement_banner_en">
                                    <p><span>@lang('custom_admin.lab_file_dimension') 450px X 300px</span></p>
                                    <input type="hidden" value="{{ $data['advertisement_banner_en'] }}" name="advertisement_banner_en_updated">
                                        <div class="logo-wrap-div @if(empty($data['advertisement_banner_en_url'])) hide @endif">
                                            <img src="{{ $data['advertisement_banner_en_url'] }}" class="logo-site-setting"> 
                                            <a class="remove-image-logos" href="javascript:void(0)" style="display: inline;">×</a>
                                        </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">@lang('custom_admin.advertising_image_de')(de)<span class="red_star">*</span></label>
                                    <input type="file" class="form-control" name="advertisement_banner_de">
                                    <p><span>@lang('custom_admin.lab_file_dimension') 450px X 300px</span></p>
                                    <input type="hidden" value="{{ $data['advertisement_banner_de'] }}" name="advertisement_banner_de_updated"> 
                                    <div class="img-wrap-div @if(empty($data['advertisement_banner_de'])) hide @endif">
                                        <img src="{{ $data['advertisement_banner_de_url'] }}"  class="header_picture-site-setting"> 
                                        <a class="remove-image-logos" href="javascript:void(0)" style="display: inline;">×</a>
                                    </div>
                                </div>
                            </div>
                       </div>
                        <div class="row">                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_facebook_link')</label>
                                    {{ Form::text('facebook_link', $data['facebook_link'], array(
                                                                'id' => 'facebook_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '')) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="LinkedInLink">@lang('custom_admin.lab_linkedin_link')</label>
                                    {{ Form::text('linkedin_link', $data['linkedin_link'], array(
                                                                'id' => 'linkedin_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="YouTubeLink">@lang('custom_admin.lab_youtube_link')</label>
                                    {{ Form::text('youtube_link', $data['youtube_link'], array(
                                                                'id' => 'youtube_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="GooglePlusLink">@lang('custom_admin.lab_gplus_link')</label>
                                    {{ Form::text('googleplus_link', $data['googleplus_link'], array(
                                                                'id' => 'googleplus_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="TwitterLink">@lang('custom_admin.lab_twitter_link')</label>
                                    {{ Form::text('twitter_link', $data['twitter_link'], array(
                                                                'id' => 'twitter_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_instagram_link')</label>
                                    {{ Form::text('instagram_link', $data['instagram_link'], array(
                                                                'id' => 'instagram_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="TwitterLink">@lang('custom_admin.lab_app_store_link')</label>
                                    {{ Form::text('app_store_link', $data['app_store_link'], array(
                                                                'id' => 'app_store_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_play_store_link')</label>
                                    {{ Form::text('play_store_link', $data['play_store_link'], array(
                                                                'id' => 'play_store_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_pinterest_link')</label>
                                    {{ Form::text('pinterest_link', $data['pinterest_link'], array(
                                                                'id' => 'pinterest_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_rss_link')</label>
                                    {{ Form::text('rss_link', $data['rss_link'], array(
                                                                'id' => 'rss_link',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>                            
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_default_meta_title')</label>
                                    {{ Form::text('default_meta_title', $data['default_meta_title'], array(
                                                                'id' => 'default_meta_title',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_default_meta_keyword')</label>
                                    {{ Form::textarea('default_meta_keywords', $data['default_meta_keywords'], array(
                                                'id' => 'default_meta_keywords',
                                                'class' => 'form-control',
                                                'rows' => 4,
                                                'cols' => 4,
                                                'placeholder' => '' )) }}
                                </div>
                            </div>                            
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_default_meta_description')</label>
                                    {{ Form::textarea('default_meta_description', $data['default_meta_description'], array(
                                                                'id' => 'default_meta_description',
                                                                'class' => 'form-control',
                                                                'rows' => 4,
                                                                'cols' => 4,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_address')</label>
                                    {{ Form::textarea('address', $data['address'], array(
                                                                'id' => 'address',
                                                                'class' => 'form-control',
                                                                'rows' => 4,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_phone_number')</label>
                                    {{ Form::text('phone_no', $data['phone_no'], array(
                                                                'id' => 'phone_no',
                                                                'class' => 'form-control',
                                                                'rows' => 4,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_delivery_delay')<span class="red_star">*</span></label>
                                    {{ Form::number('min_delivery_delay_display', $data['min_delivery_delay_display'], array(
                                                                'id' => 'min_delivery_delay_display',
                                                                'class' => 'form-control',
                                                                'min' => 0,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimum_order_amount">@lang('custom_admin.lab_minimum_order_amount')</label>
                                    <div class="input-group" id="minimum_order_amount_div">
                                        <div class="input-group-addon">
                                            CHF
                                        </div>
                                        {{ Form::text('minimum_order_amount', $data['minimum_order_amount'], array(
                                                                'id' => 'minimum_order_amount',
                                                                'class' => 'form-control',
                                                                'placeholder' => '',
                                                                'required' => 'required' )) }}
                                    </div>                                    
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_map')</label>
                                    {{ Form::textarea('map', $data['map'], array(
                                                                'id' => 'map',
                                                                'class' => 'form-control',
                                                                'rows' => 4,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_footer_address')<span class="red_star">*</span></label>
                                    {{ Form::textarea('footer_address', $data['footer_address'], array(
                                                'id' => 'footer_address',
                                                'class' => 'form-control',
                                                'rows' => 4,
                                                'cols' => 4,
                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <?php if ($siteSettings['is_shop_close'] == 'Y') { ?>
                                    <label for="IsShopClose">@lang('custom_admin.label_is_restaurant_close')</label>
                                    <label class="switch">
                                        <input type="checkbox" name="is_shop_close" id="is_shop_close" value="Y" checked>
                                        <span class="slider round"></span>
                                    </label>
                                <?php } else { ?>
                                    <label for="IsShopClose">@lang('custom_admin.label_is_restaurant_open')</label>
                                    <label class="switch">
                                        <input type="checkbox" name="is_shop_close" id="is_shop_close" value="Y">
                                        <span class="slider round"></span>
                                    </label>
                                <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_pin_code_expiry_time') (@lang('custom_admin.lab_in_minutes'))<span class="red_star">*</span></label>
                                    {{ Form::number('pincode_expiry_time', $data['pincode_expiry_time'], array(
                                                                'id' => 'pincode_expiry_time',
                                                                'class' => 'form-control',
                                                                'min' => 0,
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_restaurant_speciality')</label>
                                    {{ Form::textarea('restaurant_speciality', $data['restaurant_speciality'], array(
                                                'id' => 'restaurant_speciality',
                                                'class' => 'form-control',
                                                'rows' => 4,
                                                'cols' => 4,
                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">@lang('custom_admin.lab_mwst_number')</label>
                                    {{ Form::text('mwst_number', $data['mwst_number'], array(
                                                                'id' => 'mwst_number',
                                                                'class' => 'form-control',
                                                                'placeholder' => '' )) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">@lang('custom_admin.btn_submit')</button>
                                <a href="{{ route('admin.'.\App::getLocale().'.dashboard') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection
 