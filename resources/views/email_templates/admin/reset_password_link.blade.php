@extends('email_templates.layouts.app_email')
  	@section('content')
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="color:#141414; font-size:15px;"> @lang('custom_admin.label_hello') {{ $user->first_name }},</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><a href="{{route('admin.'.\App::getLocale().'.reset-password', $encryptedString)}}">@lang('custom_admin.label_click_here')</a> @lang('custom_admin.label_reset_your_password').</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="color:#141414; font-size:15px; line-height: 20px;">
				@lang('custom.label_email_thanks_regards'),<br>
				{{$siteSetting->website_title}}
				@if ($siteSetting->mwst_number != null)
					<br>{!! $siteSetting->mwst_number !!}
				@endif
			</td>
		</tr>
	</table>
    
  	@endsection