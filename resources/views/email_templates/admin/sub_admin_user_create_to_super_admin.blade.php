@extends('email_templates.layouts.app_email')
  @section('content')
    
    @php
    $roles = implode(',',$roleArray);
    @endphp
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="color:#141414; font-size:15px;"> @lang('custom_admin.label_hello') @lang('custom_admin.label_administrator'),</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>@lang('custom_admin.message_new_admin_email_to_SA')</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom_admin.label_first_name')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$user['first_name']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom_admin.label_last_name')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$user['last_name']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom_admin.label_email')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$user['email']}}</td>
            </tr>
            <tr>
              <td width="25%" align="left" valign="top" style="color:#141414; font-weight:bold; line-height:20px;">@lang('custom_admin.lab_subadmin_role')</td>
              <td width="2%" align="left" valign="top" style="line-height:20px;">:</td>
              <td width="73%" align="left" valign="top" style="line-height:20px;">{{$roles}}</td>
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