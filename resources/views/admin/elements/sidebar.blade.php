@php
$getAllRoles = AdminHelper::getUserRoleSpecificRoutes();
$isSuperAdmin = false;
if (\Auth::guard('admin')->user()->id == 1 || \Auth::guard('admin')->user()->type == 'SA') {
    $isSuperAdmin = true;
}
// echo '<pre>'; print_r($getAllRoles); die;

$currentPageMergeRoute = explode('admin.'.\App::getLocale().'.',Route::currentRouteName());
if (count($currentPageMergeRoute) > 0) {
    $currentPage = $currentPageMergeRoute[1];
} else {
    $currentPage = Route::currentRouteName();
}
@endphp

<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('js/admin/dist/img/avatar5.png') }}" class="img-circle" alt="{{Auth::guard('admin')->user()->full_name}}">
        </div>
        <div class="pull-left info">
            <p>{{Auth::guard('admin')->user()->full_name}}</p>
        </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
        <li class="header"><strong>@lang('custom_admin.lab_main_menu')</strong></li>
        <li @if ($currentPage == 'dashboard')class="active" @endif>
            <a href="{{route('admin.'.\App::getLocale().'.dashboard')}}">
                <i class="fa fa-dashboard"></i> <span>@lang('custom_admin.lab_dashboard')</span>
            </a>
        </li>

    <!-- Live Order Start -->
    @php
    $liveOrderRoutes = ['liveOrder.live-order-list'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('liveOrder.live-order-list',$getAllRoles)) )
        <li @if ($currentPage == 'liveOrder.live-order-list')class="active" @endif>
            <a href="{{route('admin.'.\App::getLocale().'.liveOrder.live-orders')}}" class="blink_text">
                <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> <span>@lang('custom_admin.lab_live_order_list')</span>
            </a>
        </li>
    @endif
    <!-- Live Orders end -->
    
    <!-- User management Start -->
    @php
    $userRoutes = ['user.list','user.show-all','user.add','user.edit'];    
    @endphp
    @if ( ($isSuperAdmin) || (in_array('user.list', $getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $userRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-users" aria-hidden="true"></i>
                <span>@lang('custom_admin.lab_user_management')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $userRoutes))style="display: block;" @endif>

                @if ( ($isSuperAdmin) || (in_array('user.list', $userRoutes)) )
                    <li @if (in_array($currentPage, $userRoutes))class="active" @endif><a href="{{ route('admin.'.\App::getLocale().'.user.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a></li>
                @endif

            </ul>
        </li>
    @endif
    <!-- User management End -->

    <!-- category management Start -->
    @php
    $categoryRoutes = ['category.list','category.show-all','category.add','category.edit','category.sort-category'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('category.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $categoryRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-book" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_category_management')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $categoryRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('category.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $categoryRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.category.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('category.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $categoryRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.category.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('sort-category',$getAllRoles)) )
                    <li @if (in_array($currentPage, $categoryRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.category.sort-category') }}"><i class="fa fa-sort" aria-hidden="true"></i> @lang('custom_admin.lab_sort')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- category management End -->

    <!-- Product management Start -->
    @php
    $productRoutes = ['product.list','product.show-all','product.add','product.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('product.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $productRoutes)) menu-open @endif">    
            <a href="#">
                <i class="fa fa-product-hunt" aria-hidden="true"></i>
                <span>@lang('custom_admin.lab_product_management')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $productRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('product.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $productRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.product.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('product.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $productRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.product.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif 
            </ul>
        </li>
 
        <li class="treeview @if($currentPage=='product.add-addon' || $currentPage=='product.addonlist') menu-open @endif">    
            <a href="#">
                <i class="fa fa-addon-hunt" aria-hidden="true">A</i>
                <span>@lang('custom_admin.lab_addon_managerment')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if($currentPage=='product.add-addon' || $currentPage=='product.addonlist')  style="display:block" @endif>
                @if ( ($isSuperAdmin) || (in_array('product.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $productRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.product.addonlist') }}"><i class="fa fa-list"></i>@lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('product.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $productRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.product.add-addon') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Product management End -->

    <!-- Pin Code management Start -->
    @php
    $pinCodeRoutes = ['pinCode.list','pinCode.show-all','pinCode.add','pinCode.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('pinCode.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $pinCodeRoutes)) menu-open @endif">    
            <a href="#">
                <i class="fa fa-map-pin" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_pin_code')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $pinCodeRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('pinCode.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $pinCodeRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.pinCode.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('pinCode.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $pinCodeRoutes))class="active" @endif>                
                        <a href="{{ route('admin.'.\App::getLocale().'.pinCode.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Pin Code management End -->
    
    <!-- Coupon management Start -->
    @php
    $couponRoutes = ['coupon.list','coupon.show-all','coupon.add','coupon.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('coupon.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $couponRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_new_coupon')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $couponRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('coupon.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $couponRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.coupon.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('coupon.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $couponRoutes))class="active" @endif>                
                        <a href="{{ route('admin.'.\App::getLocale().'.coupon.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Coupon management End -->

    <!-- Order Start -->
    @php
    $orderRoutes = ['order.list','order.show-all','order.details'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('order.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $orderRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_order')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $orderRoutes))style="display: block;" @endif>
            @if ( ($isSuperAdmin) || (in_array('order.list', $getAllRoles)) )
                <li @if (in_array($currentPage, $orderRoutes))class="active" @endif>
                    <a href="{{ route('admin.'.\App::getLocale().'.order.list') }}"><i class="fa fa-list"></i>@lang('custom_admin.lab_list')</a>
                </li>
            @endif
            </ul>
        </li>
    @endif
    <!-- Orders end -->

    <!-- Review Start -->
    @php
    $reviewRoutes = ['review.list','review.show-all','review.details'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('review.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $reviewRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-star" aria-hidden="true"></i>
                <span> @lang('custom_admin.label_reviews')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $reviewRoutes))style="display: block;" @endif>
            @if ( ($isSuperAdmin) || (in_array('review.list', $getAllRoles)) )
                <li @if (in_array($currentPage, $reviewRoutes))class="active" @endif>
                    <a href="{{ route('admin.'.\App::getLocale().'.review.list') }}"><i class="fa fa-list"></i>@lang('custom_admin.lab_list')</a>
                </li>
            @endif
            </ul>
        </li>
    @endif
    <!-- Review end -->

    <!-- Drink management Start -->
    @php
    $drinkRoutes = ['drink.list','drink.show-all','drink.add','drink.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('drink.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $drinkRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-glass" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_drink')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $drinkRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('drink.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $drinkRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.drink.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('drink.add', $getAllRoles)) )
                    <li @if (in_array($currentPage, $drinkRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.drink.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Drink management End -->
    
    <!-- ingredients management Start -->
    @php
    $ingredientRoutes = ['ingredient.list','ingredient.show-all','ingredient.add','ingredient.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('ingredient.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $ingredientRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-vcard-o" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_ingredient')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $ingredientRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('ingredient.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $ingredientRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.ingredient.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('ingredient.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $ingredientRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.ingredient.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- ingredient management End -->

    <!-- Tag management Start -->
    @php
    $tagRoutes = ['tag.list','tag.show-all','tag.add','tag.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('tag.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $tagRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-tags" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_tag')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $tagRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('tag.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $tagRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.tag.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('tag.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $tagRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.tag.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Tag management End -->

    <!-- Allergen management Start -->
    @php
    $allergenRoutes = ['allergen.list','allergen.show-all','allergen.add','allergen.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('allergen.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $allergenRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-circle" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_allergen')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $allergenRoutes))style="display: block;" @endif>
            @if ( ($isSuperAdmin) || (in_array('allergen.list', $getAllRoles)) )
                <li @if (in_array($currentPage, $allergenRoutes))class="active" @endif>            
                    <a href="{{ route('admin.'.\App::getLocale().'.allergen.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('allergen.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $allergenRoutes))class="active" @endif>                
                        <a href="{{ route('admin.'.\App::getLocale().'.allergen.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Allergen management End -->

    <!-- Special Menu management Start -->
    @php
    $specialMenuRoutes = ['specialMenu.list','specialMenu.show-all','specialMenu.add','specialMenu.edit','specialMenu.sort-specialMenu'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('specialMenu.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $specialMenuRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-heart" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_special_menu')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $specialMenuRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('specialMenu.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $specialMenuRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.specialMenu.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('specialMenu.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $specialMenuRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.specialMenu.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- specialMenu management End -->

    <!-- Avatar management Start -->
    @php
    $avatarRoutes = ['avatar.list','avatar.show-all','avatar.add','avatar.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('avatar.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $avatarRoutes)) menu-open @endif">    
            <a href="#">
                <i class="fa fa-user" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_avatar')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $avatarRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('avatar.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $avatarRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.avatar.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('avatar.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $avatarRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.avatar.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Avatar management End -->

    <!-- FAQs management Start -->
    @php
    $faqRoutes = ['faq.list','faq.show-all','faq.add','faq.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('faq.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $faqRoutes)) menu-open @endif">    
            <a href="#">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_faq')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $faqRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('faq.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $faqRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.faq.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('faq.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $faqRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.faq.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- FAQs management End -->
    
    <!-- Help management Start -->
    @php
    $helpRoutes = ['help.list','help.show-all','help.add','help.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('help.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $helpRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-question-circle" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_help')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $helpRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('help.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $helpRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.help.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('help.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $helpRoutes))class="active" @endif>                
                        <a href="{{ route('admin.'.\App::getLocale().'.help.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Help management End -->

    <!-- Special Hour management Start -->
    @php
    $specialHourRoutes = ['specialHour.list','specialHour.show-all','specialHour.add','specialHour.edit'];
    @endphp
    @if ( ($isSuperAdmin) || (in_array('specialHour.list',$getAllRoles)) )
        <li class="treeview @if (in_array($currentPage, $specialHourRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_special_hour')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $specialHourRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('specialHour.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $specialHourRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.specialHour.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('specialHour.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $specialHourRoutes))class="active" @endif>                
                        <a href="{{ route('admin.'.\App::getLocale().'.specialHour.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Special Hour management End -->

    <!-- Role management Start -->
    @php
    $roleRoutes = ['role.list','role.show-all','role.add','role.edit'];
    @endphp
    @if ( ($isSuperAdmin) )
        <li class="treeview @if (in_array($currentPage, $roleRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_role_management')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $roleRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('role.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $roleRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.role.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('role.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $roleRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.role.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Role management End -->

    <!-- Sub admin management Start -->
    @php
    $subAdminRoutes = ['subAdmin.list','subAdmin.show-all','subAdmin.add','subAdmin.edit'];
    @endphp
    @if ( ($isSuperAdmin) )
        <li class="treeview @if (in_array($currentPage, $subAdminRoutes)) menu-open @endif">
            <a href="#">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
                <span> @lang('custom_admin.lab_subadmin_management')</span>
                <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>  
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $subAdminRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('subAdmin.list', $getAllRoles)) )
                    <li @if (in_array($currentPage, $subAdminRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.subAdmin.list') }}"><i class="fa fa-list"></i> @lang('custom_admin.lab_list')</a>
                    </li>
                @endif
                @if ( ($isSuperAdmin) || (in_array('subAdmin.add',$getAllRoles)) )
                    <li @if (in_array($currentPage, $subAdminRoutes))class="active" @endif>
                        <a href="{{ route('admin.'.\App::getLocale().'.subAdmin.add') }}"><i class="fa fa-plus-circle"></i> @lang('custom_admin.lab_add')</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    <!-- Sub admin management End -->

    <!-- Website management start -->  
    @php
    $siteSettingRoutes = ['site-settings'];
    $restaurantAvailabilityRoutes = ['delivery-slots'];
    $cmsRoutes = ['CMS.list','CMS.edit'];
    @endphp  
    @if (($isSuperAdmin))
        <li class="treeview @if (Route::current()->getName() == 'admin.'.\App::getLocale().'.site-settings' || Route::current()->getName() == 'admin.'.\App::getLocale().'.delivery-slots') menu-open @endif">
            <a href="#">
                <i class="fa fa-wrench" aria-hidden="true"></i>
                <span>@lang('custom_admin.lab_website_management')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu" @if (in_array($currentPage, $siteSettingRoutes) || in_array($currentPage, $restaurantAvailabilityRoutes) || in_array($currentPage, $cmsRoutes))style="display: block;" @endif>
                @if ( ($isSuperAdmin) || (in_array('site-settings', $getAllRoles)) )
                    <li @if (in_array($currentPage, $siteSettingRoutes))class="active" @endif>            
                        <a href="{{ route('admin.'.\App::getLocale().'.site-settings') }}">
                            <i class="fa fa-cog" aria-hidden="true"></i> <span>@lang('custom_admin.lab_site_settings')</span>
                        </a>
                </li>
                @endif

                <li @if (in_array($currentPage, $restaurantAvailabilityRoutes))class="active" @endif>                
                    <a href="{{ route('admin.'.\App::getLocale().'.payment-settings') }}">
                        <i class="fa fa-money" aria-hidden="true"></i> <span>@lang('custom_admin.lab_new_payment_setting')</span>
                    </a>
                </li>
                <!-- Delivery slots start -->
                @if ( ($isSuperAdmin) || (in_array('delivery-slots', $restaurantAvailabilityRoutes)) )
                <li @if (in_array($currentPage, $restaurantAvailabilityRoutes))class="active" @endif>                
                    <a href="{{ route('admin.'.\App::getLocale().'.delivery-slots') }}">
                        <i class="fa fa-clock-o" aria-hidden="true"></i> <span>@lang('custom_admin.lab_new_delivery_slots')</span>
                    </a>
                </li>
                @endif
                <!-- Cms start -->
                @if ( ($isSuperAdmin) || (in_array('CMS.list', $cmsRoutes)) )                
                <li @if (in_array($currentPage, $cmsRoutes))class="active" @endif>
                    <a href="{{ route('admin.'.\App::getLocale().'.CMS.list') }}">
                        <i class="fa fa-database" aria-hidden="true"></i> <span>CMS</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
    @endif   
    <!-- Website management end -->
    </ul>
</section>
<!-- /.sidebar -->