@extends('email_templates.layouts.app_email')
  	@section('content')

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      	<tr>
        	<td style="color:#141414; font-size:15px;"> @lang('custom_admin.label_hello') {{ $details->delivery_full_name }},</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>&nbsp;</td>
      	</tr>
      	<tr>
        	<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>              
					  	<td width="78%" align="left" valign="top" style="line-height:20px;">
							{{ trans('custom_admin.message_delivery_notification', ['orderid' => $details->unique_order_id]) }} {{$deliveryIn}} @lang('custom_admin.lab_minutes')
						</td>
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
				@lang('custom.label_email_thanks_regards'),<br>
          		{{$siteSetting->website_title}}
        	</td>
      	</tr>
    </table>
    
  @endsection