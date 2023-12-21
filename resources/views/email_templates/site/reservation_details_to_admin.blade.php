@extends('email_templates.layouts.app_email')
  @section('content')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="color:#141414; font-size:15px;"> @lang('custom.label_hello') @lang('custom.label_administrator'),</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>@lang('custom.label_reservation_mail_content')</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_date')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['reservation_date']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_time')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['delivery_time']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_people')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['people']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_name')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['name']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_email_address')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['email']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_phone')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['phone']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom.label_message')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$reservation['message']}}</td>
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