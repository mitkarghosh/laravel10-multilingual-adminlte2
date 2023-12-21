<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>{{$siteSetting->website_title}}</title>
  <style type="text/css">
  p{ margin:0; padding:12px 0 0 0; line-height:22px;}
  </style>
</head>

<body style="background:#efefef; margin:0; padding:0;">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">
    <tbody>
      <tr>
        <td align="center" valign="middle" bgcolor="#ffffff" style="padding:15px; margin:0; line-height:0;"><a target="_blank" href="{{route('site.'.\App::getLocale().'.home')}}"><img src="{{asset('images/site/logo.png')}}" alt="" style="border:0;" width="100" height="105" /></a></td>
      </tr>
      <tr>
        <td align="left" valign="top" bgcolor="#ffffff" style="color:#3c3c3c; margin:0; padding:15px 15px 30px 15px; border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;">
          @yield('content')
        </td>
      </tr>
      <tr>
        <td align="center" valign="middle" bgcolor="#f1f7ff" style="padding:20px; color:#535353; margin:0; line-height:0;">
          <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              {{-- <td width="70%" align="left" valign="middle">
                <a href="#" target="_blank" style="color:#535353; text-decoration:none;">Terms & Condition</a> | 
                <a href="#" target="_blank" style="color:#535353; text-decoration:none;">Privacy Policy</a>
              </td> --}}
              <td width="100%" align="left" valign="middle">
                <a href="{{$siteSetting->facebook_link}}" target="_blank"><img src="{{asset('images/site/facebook.png')}}" alt="" style="border:0;" /></a>
                <a href="{{$siteSetting->instagram_link}}" target="_blank"><img src="{{asset('images/site/instagram.png')}}" alt="" style="border:0;" /></a>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="middle" bgcolor="#1279cf" style="padding:20px; color:#ffffff; margin:0; line-height:0;">© @lang('custom.label_email_copyright') {{date('Y')}}. @lang('custom.label_email_all_right_reserved').</td>
      </tr>
    </tbody>
  </table>
</body>
</html>
