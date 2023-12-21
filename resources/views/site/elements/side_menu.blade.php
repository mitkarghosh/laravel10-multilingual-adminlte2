@php
$currentLang = \App::getLocale();
@endphp
<aside class="col-md-3 col-sm-4 stickySidebar accountSidebar">
    <div class="tt_sideblock">
        <h2 class="subheading"><i class="icon-settings"></i> @lang('custom.lab_your_account')</h2>
        <nav class="side_list">
            <ul>
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
            </ul>
        </nav>
    </div>
</aside>