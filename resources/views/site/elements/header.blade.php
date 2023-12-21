@php
$siteSettingData = Helper::getSiteSettings();
$siteNavLang = trans('custom.lang_english');
if (App::getLocale() == 'ar') {
	$siteNavLang = trans('custom.lang_arabic');
}
$currentLang = App::getLocale();
$pincode = '';
// if (\Session::get('pincode') != '') {
// 	$pincode = \Session::get('pincode');
// }
if (Cookie::get('pincode') != '') {
	$pincode = Cookie::get('pincode');
}
$pnglogo=Helper::getSettingImage('png_logo');
@endphp
<header class="mainHeader @if(Route::current()->getName() == 'site.'.$currentLang.'.home' || Route::current()->getName() == 'site.'.$currentLang.'.info' || Route::current()->getName() == 'site.'.$currentLang.'.reviews') header_tr @else not_header_tr @endif">
	<section class="header_main">
		<div class="container">
			<div class="logoWrap main-logo-header">
				<a aria-label="{{$siteSettingData->website_title}}" @if($pnglogo) style="background: url({{Helper::getSettingImage('png_logo')}}) no-repeat center;" @endif href="{{route('site.'.$currentLang.'.home')}}" class="logo">
					<img src="@if(Helper::getSettingImage('png_logo')) {{Helper::getSettingImage('png_logo')}} @else{{ asset('images/site/logo_top.png') }} @endif" alt="{{$siteSettingData->website_title}}">
				</a>
				{{-- Popup when click on Pincode --}}
				@if (Cookie::get('pincode') != '')
					<a href="javascript: void(0);" id="click_to_popup"><i class="fa fa-map-marker"></i> {{Cookie::get('pincode')}}</a>
				@endif
			</div>

			<div class="nav_wrapper">
				<nav class="nav_menu userHeader">
					<ul class="clearfix">
						<li class="mobMenu"><a href="{{route('site.'.$currentLang.'.home')}}"><span>@lang('custom.label_mob_home')</span></a></li>
					@if(Auth::user())
						@php
						$userAvatar = URL:: asset('images/site/sample/avatar5.jpg');
						if (Auth::user()->avatarDetails != null) {
							if (file_exists(public_path('/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image))) {
								$userAvatar = URL::to('/').'/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image;
							}
						}
						@endphp
						<li>
							<span class="sub-navItem"><figure class="avatar avatar_update" style="background-image: url({{$userAvatar}});"></figure>{{Auth::user()->full_name}}</span>
							<ul class="sub-menu">
								<li><div class="user_ac"><figure class="avatar avatar_update" style="background-image: url({{$userAvatar}});"></figure>{{Auth::user()->full_name}}</div></li>
								<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.personal-details') class="active" @endif>
									<a href="{{route('site.'.$currentLang.'.users.personal-details')}}">@lang('custom.lab_personal_details')</a>
								</li>
								<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.delivery-address' || Route::current()->getName() == 'site.'.$currentLang.'.users.add-address' || Route::current()->getName() == 'site.'.$currentLang.'.users.edit-address') class="active" @endif>
									<a href="{{route('site.'.$currentLang.'.users.delivery-address')}}">@lang('custom.lab_new_delivery_address')</a>
								</li>
								<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.notifications') class="active" @endif>
									<a href="{{route('site.'.$currentLang.'.users.notifications')}}">@lang('custom.lab_notifications')</a>
								</li>								
								<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.orders-reviews') class="active" @endif>
									<a href="{{route('site.'.$currentLang.'.users.orders-reviews')}}">@lang('custom.lab_orders_reviews')</a>
								</li>
								<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.change-user-password') class="active" @endif>
									<a href="{{route('site.'.$currentLang.'.users.change-user-password')}}">@lang('custom.lab_change_password')</a>
								</li>
								<li>
									<a href="{{route('site.'.$currentLang.'.users.logout')}}">@lang('custom.lab_log_out')</a>
								</li>
							</ul>
						</li>
					@else
						<li><a href="{{route('site.'.$currentLang.'.users.login')}}"><span>@lang('custom.lab_login')</span></a></li>
						<li><a href="{{route('site.'.$currentLang.'.users.register')}}"><span>@lang('custom.lab_sign_up')</span></a></li>
					@endif
						<li><a href="{{route('site.'.$currentLang.'.help')}}"><span>@lang('custom.label_help')</span></a></li>
						<li class="tt_language">
							<span class="sub-navItem" style="text-transform: uppercase;">{{strtoupper(App::getLocale())}} <i class="icon-arrow-down subarrow"></i></span>
							<ul class="sub-menu">
								<li @if(App::getLocale() == 'de') class="active" @endif><span class="dashboard_website_language" data-lang="de">@lang('custom.lang_dutch')</span></li>
								<li @if(App::getLocale() == 'en') class="active" @endif><span class="dashboard_website_language" data-lang="en">@lang('custom.lang_english')</span></li>
							</ul>
						</li>
					</ul>
				</nav>
				<span class="responsive_btn"><span></span></span>
			</div>
		</div>
	</section>
	<input type="hidden" name="site_link" id="site_link" value="{{ url('/') }}" />
	<input type="hidden" name="site_lang" id="site_lang" value="{{ \App::getLocale() }}" />
	
	<input type="hidden" name="pincode" id="pincode" value="{{$pincode}}" />

	@php
	$getCartStatus = 0;
	if(Route::current()->getName() == 'site.'.$currentLang.'.home' || Route::current()->getName() == 'site.'.$currentLang.'.info' || Route::current()->getName() == 'site.'.$currentLang.'.reviews') {
		$getCartStatus = 1;
	}
	@endphp
	<input type="hidden" name="get_cart" id="get_cart" value="{{$getCartStatus}}" />
</header>
