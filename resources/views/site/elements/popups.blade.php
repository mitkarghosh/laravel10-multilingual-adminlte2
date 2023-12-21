@php
$allergenList       = Helper::getAllergenList();
$siteSettingsData   = Helper::getSiteSettings();
$clang=\App::getLocale();
$imageadven=($clang=='en')?Helper::getSettingImage('adv_en'):Helper::getSettingImage('adv_de'); 
@endphp
@if($imageadven)
<input type="hidden" class="offer-en-banner" value="{{base64_encode(Helper::getSettingImage('adv_en'))}}">
<input type="hidden" class="offer-de-banner" value="{{base64_encode(Helper::getSettingImage('adv_de'))}}">
@endif
<div class="tt_modal text-center" id="allergy_modal">
    <div class="tt_modal_container">
        <div class="tt_modal_main">
            <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
            <div class="tt_modal_header">@lang('custom.text_allergen_heading')</div>
            <div class="tt_modal_body">
                <p>{!! trans('custom.text_allergen_popup', ['phoneNumber' => $siteSettingsData['phone_no']]) !!}</p>
                <div class="allergy_list">
                    <h3 class="heading">@lang('custom.text_allergen_description')</h3>
                @if ($allergenList->count() > 0)
                    <ul class="ul row">
                    @foreach($allergenList as $item)
                        @php
                        $imgPath = URL:: asset('images').'/site/'.Helper::NO_IMAGE;
                        if(file_exists(public_path('/uploads/allergen/thumbs/'.$item->image))) {
                            $imgPath = URL::to('/').'/uploads/allergen/thumbs/'.$item->image;
                        }
                        @endphp
                        <li class="col-sm-4">
                            <div class="allergy_item"><img src="{{$imgPath}}" alt="{{$item->local[0]->local_title}}"> {{$item->local[0]->local_title}}</div>
                        </li>
                    @endforeach
                    </ul>
                @else
                    <p>@lang('custom.message_no_records_found')</p>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pincode start --}}
<div class="tt_modal text-center" id="pincode_modal">
    <div class="tt_modal_container">
        <div class="tt_modal_main">
            <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
            <div class="tt_modal_header">@lang('custom.label_pin_code')</div>
            <div class="tt_modal_body">
                <p>@lang('custom.text_pin_code'). <a href="{{route('site.'.\App::getLocale().'.info')}}">@lang('custom.label_click_here')</a> @lang('custom.label_click_here1').</p>
                <div class="form_wrap form_box text-center ">
                    <form method="POST" action="javascript:void(0)" name="pinCodeForm" id="pinCodeForm" novalidate="" enctype="multipart/form-data">
                        <ul class="row">
                            <li class="col-sm-12">
                                <div class="form-group">
                                    <label class="labelWrap">
                                        <span>@lang('custom.label_pin_code') *</span>
                                        <input type="text" name="pin_code" id="pin_code" placeholder="@lang('custom.label_pin_code') *" value="{{Cookie::get('pincode')}}">
                                    </label>
                                    <div class="help_block"></div>
                                </div>
                            </li>
                            <li class="col-sm-12">
                                <button type="submit" class="btn btn-width">@lang('custom.submit')</button>
                            </li>
                        </ul>
                    {{Form::close()}}
                    <span id="pin_code_available_message" style="visibility:hidden">&nbsp;</span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Pincode end --}}

{{-- Guest checkout (Email exist so need to login) start --}}
<div class="tt_modal text-center" id="guest_login_modal">
    <div class="tt_modal_container">
        <div class="tt_modal_main">
            <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
            <div class="tt_modal_header">@lang('custom.lab_login')</div>
            <div class="tt_modal_body">
                <div class="form_wrap text-center mt30">
                    <form method="POST" action="javascript:void(0)" name="guestLoginForm" id="guestLoginForm" novalidate="" enctype="multipart/form-data" autocomplete="off">
                        {!! csrf_field() !!}
                        <ul class="row">
                            <li class="col-xs-12">
                                <div class="form-group">
                                    <label class="labelWrap">
                                        <span>@lang('custom.label_email') *</span>
                                        <input type="email" name="email" id="guest_login_email" value="{{Session::get('guest_email')}}" placeholder="@lang('custom.placeholder_email') *">
                                    </label>
                                    <div class="help_block"></div>
                                </div>
                            </li>
                            <li class="col-xs-12">
                                <div class="form-group">
                                    <label class="labelWrap showPass">
                                        <span>@lang('custom.label_password') *</span>
                                        <input type="password" name="password" id="guest_login_password" value="" placeholder="@lang('custom.placeholder_password') *">
                                        <i class="showPassIcon"></i>
                                    </label>
                                    <div class="help_block"></div>
                                </div>
                            </li>
                            {{-- <li class="col-xs-6">
                                <div class="form-group">
                                    <div class="labelWrap">
                                        <label class="input_check">
                                            <input type="checkbox" name="" checked>
                                            <span>Stay logged in</span>
                                        </label>
                                    </div>
                                    <div class="help_block"></div>
                                </div>
                            </li>
                            <li class="col-xs-6">
                                <div class="form-group text-right lh30">
                                    <a href="forgot-password.html"><strong>Forgot password?</strong></a>
                                </div>
                            </li> --}}
                            <li class="col-xs-12">
                                <button type="submit" class="btn btn-width">@lang('custom.lab_login')</button>
                            </li>
                            {{-- <li class="col-xs-12">
                                <div>Don't have an account? <a href="register.html"><strong>Sign up</strong></a></div>
                            </li> --}}
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Guest checkout (Email exist so need to login) end --}}

<!-- Advertise -->
{{-- offer model popup --}}
@if($imageadven)
    @if(Route::current()->getName() == 'site.'.\App::getLocale().'.home')
        <div class="tt_modal text-center" id="offer-popup">
            <div class="tt_modal_container" >
                <div class="tt_modal_main p0">
                    <span class="tt_modal_close ti-close save-close-banner" data-dismiss="tt_modal"></span>
                    <div class="tt_modal_body">
                        <div class="form_wrap text-center">
                            <img class="upload-img" src="{{$imageadven}}">
                        </div>
                        <div class="modal-footer-check-btn">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="dont-show-avd">
                        <label class="form-check-label" for="flexCheckDefault">
                           <span>@lang('custom.dont_show_anymore') </span>
                        </label>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
<!-- Advertise -->