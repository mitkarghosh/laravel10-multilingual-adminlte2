@php
$siteSettings = Helper::getSiteSettings();
$appStoreLink = $playStoreLink = '';
if ($siteSettings->app_store_link != null) {
    $appStoreLink = $siteSettings->app_store_link;
}
if ($siteSettings->play_store_link != null) {
    $playStoreLink = $siteSettings->play_store_link;
}
@endphp
<div class="whole-area" id="whole-area" style="display: none;">
    <div class="loader" id="loader-1"></div>
</div>

<footer class="mainFooter">
    <section class="ftop">
        <div class="container">
            <div class="tt_fleft">
            @php
            if ($appStoreLink != '' || $playStoreLink != '') {
            @endphp
                <h2 class="subheading">@lang('custom.label_our_app')</h2>
                <div class="tt_app_btn">
                    @php
                    if ($appStoreLink != '') {
                    @endphp
                    <a href="{!! $appStoreLink !!}" target="_blank" rel="noopener noreferrer nofollow" aria-label="App Store" class="tt_applestore"></a>
                    @php
                    }
                    if ($playStoreLink != '') {
                    @endphp
                    <a href="{!! $playStoreLink !!}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Google Play" class="tt_googleplay"></a>
                    @php
                    }
                    @endphp
                </div>
            @php
            }
            @endphp
            </div>
            <div class="tt_fright text-right">
                <h2 class="subheading">@lang('custom.label_follow_us')</h2>
                <div class="tt_social">
                    @if($siteSettings->facebook_link)
                    <a href="{{$siteSettings->facebook_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Facebook" class="tt_facebook"><i class="icon-social-facebook"></i></a>
                    @endif
                    @if($siteSettings->instagram_link)
                    <a href="{{$siteSettings->instagram_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Instagram" class="tt_instagram"><i class="icon-social-instagram"></i></a>
                    @endif
                    @if($siteSettings->linkedin_link)
                    <a href="{{$siteSettings->linkedin_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Linkedin" class="tt_linkedin"><i class="icon-social-linkedin"></i></a>
                    @endif
                    @if($siteSettings->youtube_link)
                    <a href="{{$siteSettings->youtube_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Youtube" class="tt_youtube"><i class="icon-social-youtube"></i></a>
                    @endif
                    @if($siteSettings->pinterest_link)
                    <a href="{{$siteSettings->pinterest_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="Pinterest" class="tt_pinterest"><i class="icon-social-pinterest"></i></a>
                    @endif
                    @if($siteSettings->googleplus_link)
                    <a href="{{$siteSettings->googleplus_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="googleplus" class="tt_pinterest"><i class="fa fa-google-plus"></i></a>
                    @endif
                    @if($siteSettings->twitter_link)
                    <a href="{{$siteSettings->twitter_link}}" target="_blank" rel="noopener noreferrer nofollow" aria-label="googleplus" class="tt_pinterest"><i class="icon-social-twitter"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="copyright">
        <div class="container">
            <div class="tt_fleft">
                {!!$siteSettings->footer_address!!}
                <div class="f_links">
                    <a href="{{ route('site.'.\App::getLocale().'.privacy-policy') }}" target="_blank">@lang('custom.label_privacy_policy')</a>
                    <a href="{{ route('site.'.\App::getLocale().'.colofon') }}" target="_blank">@lang('custom.label_colofon')</a>
                </div>
            </div>
            <div class="tt_fright">
                <ul class="tt_payment_modes">
                    <li><div class="tt_payment payment_visa"></div></li>
                    <li><div class="tt_payment payment_mastercard"></div></li>
                    <li><div class="tt_payment payment_maestro"></div></li>
                    <li><div class="tt_payment payment_diepost"></div></li>
                    <li><div class="tt_payment payment_cashpayment"></div></li>
                </ul>
            </div>
        </div>
    </section>
    <input type="hidden" name="website_link" id="website_link" value="{{ url('/') }}" />
    <input type="hidden" name="website_lang" id="website_lang" value="{{ \App::getLocale() }}" />
</footer>