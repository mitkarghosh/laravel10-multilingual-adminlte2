<div class="whole-area" id="whole-area" style="display: none;">
	<div class="loader" id="loader-1"></div>
</div>

<input type="hidden" name="website_admin_link" id="website_admin_link" value="{{ url('/') }}" />

<div class="pull-right hidden-xs">
    <b>{{ Helper::getAppName() }} @lang('custom_admin.message_admin_panel')</b>
</div>
&copy; <strong><a href="https://sunna-informatik.ch" target="_blank">@lang('custom_admin.message_copyright')</a> {{ date('Y') }}.</strong> @lang('custom_admin.message_reserved').

<input type="hidden" name="website_link" id="website_link" value="{{ url('/securepanel/') }}" />
<input type="hidden" name="website_lang" id="website_lang" value="{{ \App::getLocale() }}" />