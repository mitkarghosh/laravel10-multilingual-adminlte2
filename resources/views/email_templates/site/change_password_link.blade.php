@extends('email_templates.layouts.app_email')
  @section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="color:#141414; font-size:15px;"> @lang('custom.label_hello') {{ $user->first_name }},</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td> @lang('custom.label_email_message1').</td>
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
              <td width="78%" align="left" valign="top" style="line-height:20px;"><a href="{{ $app_config['appLink'].'/'.$app_config['currentLang'].'/'.$app_config['controllerName'].'/reset-password/'.$user->remember_token }}" style="padding: 10px;font-size: 15px;background-color: #1279cf;color: #FFF;border: none;border-radius: 5px;text-decoration: none;">@lang('custom.label_email_click_here')</a></td>
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
          @if ($siteSetting->mwst_number != null)
            <br>{!! $siteSetting->mwst_number !!}
          @endif
        </td>
      </tr>
    </table>
    
  @endsection