<a href="{{route('admin.'.\App::getLocale().'.dashboard')}}" class="logo">
    {{-- <span class="logo-mini"><b>{{ Helper::getAppNameFirstLetters() }}</b></span>
    <span class="logo-lg"><b>{{ Helper::getAppName() }}</b></span>
    <img src="{{ asset('images/site/logo.png') }}"/>     --}}
    <span class="logo-mini"><b>{{ Helper::getAppNameFirstLetters() }}</b></span>
    { 
    <img class="logo-lg" src="@if(Helper::getSettingImage('png_logo')) {{Helper::getSettingImage('png_logo')}} @else{{ asset('images/admin/logo_top.png') }} @endif" width="100px" height="20px" />
</a>

<nav class="navbar navbar-static-top">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">@lang('custom_admin.btn_toggle_navigation')</span>
    </a>
    
    <div class="navbar-custom-menu" style="float: left;">
        <ul class="nav navbar-nav">
            <li class="">
                <a href="{{url('/').'/'.\App::getLocale()}}" target="_blank">@lang('custom_admin.label_website') <i class="fa fa-external-link" aria-hidden="true"></i></a>
            </li>
        </ul>
    </div>

    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <!-- Language changer start -->
            <li class="dropdown messages-menu user-menu">
                <a href="#" class="dropdown-toggle admin_user_img_area" data-toggle="dropdown">
                @if (\App::getLocale() == 'en')
                    <img src="{{asset('images/admin/english.png')}}" class="user-image" alt="">
                @else
                    <img src="{{asset('images/admin/germany.png')}}" class="user-image" alt="">
                @endif
                    <i class="fa fa-angle-down arrow_size"></i>
                </a>
                <ul class="dropdown-menu language_dropdown">
                    <li>
                        <ul class="menu">
                            <li class="alternate_back_color">
                                <a href="javascript: void(0);" class="admin_website_language" data-lang="en">
                                    <div class="pull-left">
                                        <img src="{{asset('images/admin/english.png')}}" class="img-circle" alt="">
                                    </div>
                                    <h4 class="language_dropdown_margin">
                                        @lang('custom_admin.lang_english')
                                    </h4>
                                </a>
                            </li>
                            <li class="alternate_back_color">
                                <a href="javascript: void(0);" class="admin_website_language" data-lang="de">
                                    <div class="pull-left">
                                        <img src="{{asset('images/admin/germany.png')}}" class="img-circle" alt="">
                                    </div>
                                    <h4 class="language_dropdown_margin">
                                        @lang('custom_admin.lang_dutch')
                                    </h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <!-- Language changer end -->

            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle admin_user_img_area" data-toggle="dropdown">
                    <img src="{{ asset('js/admin/dist/img/avatar5.png') }}" class="user-image" alt="{{ Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name }}">
                    <span class="hidden-xs">{{ Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name }}</span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <img src="{{ asset('js/admin/dist/img/avatar5.png') }}" class="img-circle" alt="{{ Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name }}">
                        <p>
                            {{Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name}}
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="row">                            
                            <div class="col-xs-12 text-center">
                                <a class="" href="{{ route('admin.'.\App::getLocale().'.change-password') }}">@lang('custom_admin.lab_change_password')</a>
                            </div>                            
                        </div>
                    </li>
                    <!-- Menu Footer -->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="{{ route('admin.'.\App::getLocale().'.edit-profile') }}" class="btn btn-default btn-flat">@lang('custom_admin.lab_profile')</a>
                        </div>
                        <div class="pull-right">
                            <a href="{{ route('admin.'.\App::getLocale().'.logout') }}" class="btn btn-default btn-flat">@lang('custom_admin.lab_signout')</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>