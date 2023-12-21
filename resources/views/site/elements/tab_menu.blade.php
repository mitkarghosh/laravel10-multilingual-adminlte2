@php
$currentLang            = \App::getLocale();
$siteSettings           = Helper::getSiteSettings();
$gettingReviewDetails   = Helper::gettingReviews();
$gettingShopStatus      = Helper::gettingShopStatus();
$gettingShopStatusFlag  = Helper::gettingShopStatusFlag();
$logoImage=Helper::getSettingImage('logo'); 
@endphp
<section class="header_btm">
    <div class="container">
        <div class="htop">
            <a aria-label="{!! $siteSettings->website_title !!}" href="{{route('site.'.$currentLang.'.home')}}" class="logo">
                <img src="@if($logoImage){{$logoImage}}@else{{asset('images/site/logo.png')}}@endif" alt="{!! $siteSettings->website_title !!}">
            </a>
            <div class="hright">
                <h2 class="heading heading_large">@lang('custom.label_web_site_title')</h2>
                <div class="tt_fleft">
                    @if ($siteSettings->restaurant_speciality != null)
                    <ul class="divider_list">
                        {!! $siteSettings->restaurant_speciality !!}
                    </ul>
                    @endif
                    <div class="rating_wrap ">
                        <div class="rating">
                        @php
                        $explodeAllStarRating = explode('.',$gettingReviewDetails['starAvgAllRating']);                       
                        
                        $allHafStartFlag = 0;
                        for ($g = 1; $g <= $explodeAllStarRating[0]; $g++)	 {
                            echo '<i class="fa fa-star"></i>';
                        }
                        for ($y = 5 - $explodeAllStarRating[0]; $y >= 1; $y--)	 {
                            if (isset($explodeAllStarRating[1]) && $explodeAllStarRating[1] != 0 && $allHafStartFlag == 0) {
                                echo '<i class="fa fa-star-half-o"></i>';
                                $allHafStartFlag++;
                            } else {
                                echo '<i class="fa fa-star-o"></i>';
                            }								
                        }
                        @endphp
                        </div>
                        <span>{{count($gettingReviewDetails['totalReviews'])}}</span>
                    </div>
                </div>
                <div class="pt10">
                    <ul class="divider_list">
                        <li><i class="siteicon icon_scooter mr10"></i> {{trans('custom.delivery_in_mins', [ 'time' => $siteSettings->min_delivery_delay_display])}} 
                        @if ($gettingShopStatusFlag == 0 || $siteSettings->is_shop_close == 'Y')
                            {{-- ({{$gettingShopStatus}}) --}}
                            ({{ trans('custom.label_close') }})
                        @else
                            ({{ trans('custom.label_open') }})
                        @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="nav_wrapper">
            <nav class="nav_menu">
                <ul class="clearfix">
                    <li @if(Route::current()->getName() == 'site.'.$currentLang.'.home') class="active" @endif>
                        <a href="{{route('site.'.$currentLang.'.home')}}"><span>@lang('custom.label_menu')</span></a>
                    </li>
                    <li @if(Route::current()->getName() == 'site.'.$currentLang.'.reviews') class="active" @endif>
                        <a href="{{route('site.'.$currentLang.'.reviews')}}"><span>@lang('custom.label_reviews')</span></a>
                    </li>
                    <li @if(Route::current()->getName() == 'site.'.$currentLang.'.info') class="active" @endif>
                        <a href="{{route('site.'.\App::getLocale().'.info')}}"><span>@lang('custom.label_info')</span></a>
                    </li>
                    <li @if(Route::current()->getName() == 'site.'.$currentLang.'.reservation') class="active" @endif>
                        <a href="{{route('site.'.\App::getLocale().'.reservation')}}"><span>@lang('custom.label_reservation')</span></a>
                    </li>
                    <li class="mobNone">
                        <a href="https://schiffbinningen.ch/restaurant/" class="btn" target="_blank"><span>Zum Restaurant</span></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</section>