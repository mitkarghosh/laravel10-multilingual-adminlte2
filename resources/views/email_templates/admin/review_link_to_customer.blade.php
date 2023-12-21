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
        	<td>@lang('custom_admin.label_order_review_link').</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>              
					  	<td width="78%" align="left" valign="top" style="line-height:20px;"><a href="{!! $app_config['reviewLink'] !!}" style="padding: 10px;font-size: 15px;background-color: #1279cf;color: #FFF;border: none;border-radius: 5px;text-decoration: none;">@lang('custom.label_email_click_here')</a></td>
					</tr>
				</table>
        	</td>
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
				@lang('custom.label_email_thanks_regards')<br>
          		{{$siteSetting->website_title}}
        	</td>
      	</tr>
    </table>
    
  @endsection