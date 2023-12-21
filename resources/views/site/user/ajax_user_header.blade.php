@php
$currentLang = \App::getLocale();
$userAvatar = URL:: asset('images/site/sample/avatar5.jpg');
if (Auth::user()->avatarDetails != null) {
	if (file_exists(public_path('/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image))) {
		$userAvatar = URL::to('/').'/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image;
	}
}
@endphp
<ul class="clearfix">
	<li>
		<span class="sub-navItem"><figure class="avatar avatar_update" style="background-image: url({{$userAvatar}});"></figure>{{Auth::user()->full_name}}</span>
		<ul class="sub-menu">
			<li><div class="user_ac"><figure class="avatar avatar_update" style="background-image: url({{$userAvatar}});"></figure>{{Auth::user()->full_name}}</div></li>
			<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.personal-details') class="active" @endif>
				<a href="{{route('site.'.$currentLang.'.users.personal-details')}}">@lang('custom.lab_personal_details')</a>
			</li>
			<li @if(Route::current()->getName() == 'site.'.$currentLang.'.users.delivery-address' || Route::current()->getName() == 'site.'.$currentLang.'.users.add-address' || Route::current()->getName() == 'site.'.$currentLang.'.users.edit-address') class="active" @endif>
				<a href="{{route('site.'.$currentLang.'.users.delivery-address')}}">@lang('custom.lab_delivery_address')</a>
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
	<li><a href="{{route('site.'.$currentLang.'.help')}}"><span>@lang('custom.label_help')</span></a></li>
	<li class="tt_language">
		<span class="sub-navItem">{{strtoupper(App::getLocale())}} <i class="icon-arrow-down subarrow"></i></span>
		<ul class="sub-menu">
			<li @if(App::getLocale() == 'de') class="active" @endif><span class="dashboard_website_language" data-lang="de">@lang('custom.lang_dutch')</span></li>
			<li @if(App::getLocale() == 'en') class="active" @endif><span class="dashboard_website_language" data-lang="en">@lang('custom.lang_english')</span></li>
		</ul>
	</li>
</ul>