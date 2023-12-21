<section class="common_banner">
    <div class="bannerbox">
    @php 
    $logo=Helper::getSettingImage('logo');  
    $headerImage=Helper::getSettingImage('header');
    $siteSetting = Helper::getSiteSettings(); 
    @endphp
    @if(Route::current()->getName() == 'site.'.\App::getLocale().'.help')
        <div class="logo"><img src="@if($logo){{$logo}}@else{{asset('images/site/logo.png')}}@endif" alt="{{$siteSetting->website_title}}}}"></div>
    @endif
        <figure class="bannerimg">
            <img class="lazy" src="@if($headerImage){{$headerImage}}@else{{asset('images/site/blank.png')}}@endif" data-src="@if($headerImage){{$headerImage}}@else{{asset('images/site/sample/banner.jpg')}}@endif" alt="">
        </figure>
    </div>
</section>