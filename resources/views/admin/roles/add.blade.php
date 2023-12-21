@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<section class="content-header">
    <h1>
        {{ $page_title }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{ route('admin.'.\App::getLocale().'.role.list') }}"><i class="fa fa-lock"></i> @lang('custom_admin.lab_role_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                @include('admin.elements.notification')

                {{ Form::open(array(
                                'method'=> 'POST',
                                'class' => '',
                                'route' => ['admin.'.\App::getLocale().'.role.addsubmit'],
                                'name'  => 'createRoleForm',
                                'id'    => 'createRoleForm',
                                'files' => true,
                                'novalidate' => true)) }}
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">@lang('custom_admin.lab_role_name')<span class="red_star">*</span></label>
                                    {{ Form::text('name', null, array(
                                                                'id' => 'name',
                                                                'placeholder' => '',
                                                                'class' => 'form-control',
                                                                'required' => 'required'
                                                                )) }}
                                </div>
                            </div>                            
                        </div>
                    
            
                    <!-- Permission section start -->
                    <div class="add-edit-permission-wrap">
                        <div class="permission-title">
                            <div>
                                <input type="checkbox" class="mainSelectDeselectAll"> <span>@lang('custom_admin.label_role_select_deselect_all')</span>
                            </div>
                        </div>
                @if (count($routeCollection) > 0)
                    @foreach ($routeCollection as $group => $groupRow)
                        @php
                        $mainLabel = $group;
                        if (trim($mainLabel) == 'user' || trim($mainLabel) == 'User') {
                            $mainLabel = trans('custom_admin.label_role_user');
                        } else if (trim($mainLabel) == 'cms' || trim($mainLabel) == 'CMS') {
                            $mainLabel = trans('custom_admin.label_role_cms');
                        } else if (trim($mainLabel) == 'category' || trim($mainLabel) == 'Category') {
                            $mainLabel = trans('custom_admin.label_role_category');
                        } else if (trim($mainLabel) == 'drink' || trim($mainLabel) == 'Drink') {
                            $mainLabel = trans('custom_admin.label_role_drink');
                        } else if (trim($mainLabel) == 'ingredient' || trim($mainLabel) == 'Ingredient') {
                            $mainLabel = trans('custom_admin.label_role_ingredient');
                        } else if (trim($mainLabel) == 'tag' || trim($mainLabel) == 'Tag') {
                            $mainLabel = trans('custom_admin.label_role_tag');
                        } else if (trim($mainLabel) == 'allergen' || trim($mainLabel) == 'Allergen') {
                            $mainLabel = trans('custom_admin.label_role_allergen');
                        } else if (trim($mainLabel) == 'product' || trim($mainLabel) == 'Product') {
                            $mainLabel = trans('custom_admin.label_role_product');
                        } else if (trim($mainLabel) == 'specialMenu' || trim($mainLabel) == 'SpecialMenu') {
                            $mainLabel = trans('custom_admin.label_role_special_menu');
                        } else if (trim($mainLabel) == 'avatar' || trim($mainLabel) == 'Avatar') {
                            $mainLabel = trans('custom_admin.label_role_avatar');
                        } else if (trim($mainLabel) == 'pinCode' || trim($mainLabel) == 'PinCode') {
                            $mainLabel = trans('custom_admin.label_role_pincode');
                        } else if (trim($mainLabel) == 'faq' || trim($mainLabel) == 'Faq') {
                            $mainLabel = trans('custom_admin.label_role_faq');
                        } else if (trim($mainLabel) == 'help' || trim($mainLabel) == 'Help') {
                            $mainLabel = trans('custom_admin.label_role_help');
                        } else if (trim($mainLabel) == 'order' || trim($mainLabel) == 'Order') {
                            $mainLabel = trans('custom_admin.label_role_order');
                        } else if (trim($mainLabel) == 'liveOrder' || trim($mainLabel) == 'LiveOrder') {
                            $mainLabel = trans('custom_admin.label_role_live_order');
                        } else if (trim($mainLabel) == 'review' || trim($mainLabel) == 'Review') {
                            $mainLabel = trans('custom_admin.label_role_review');
                        } else if (trim($mainLabel) == 'coupon' || trim($mainLabel) == 'Coupon') {
                            $mainLabel = trans('custom_admin.label_role_coupon');
                        } else if (trim($mainLabel) == 'specialHour' || trim($mainLabel) == 'SpecialHour') {
                            $mainLabel = trans('custom_admin.lab_special_hour');
                        }
                        @endphp
                        <div class="col-md-12 individual_section">
                            <div class="permission-title">
                                <h2>{{ ucwords($mainLabel) }}</h2>
                                <div>
                                    <input type="checkbox" data-parentRoute="{{ $group }}" class="select_deselect selectDeselectAll"> <span>@lang('custom_admin.label_role_select_deselect_all')</span>
                                </div>
                            </div>
                            <div class="permission-content section_class">
                                <ul>
                                @php $listOrIndex = 1; @endphp
                                @foreach($groupRow as $row)
                                    @php
                                    $groupClass = str_replace(' ','_',$group);

                                    $labelName = str_replace(['admin.','.','-',$group], ['',' ',' ',''], $row['path']);
                                    if (strpos(trim($labelName), 'index') !== false) {
                                        $labelName = str_replace('index','List',$labelName);
                                    }
                                    
                                    $subClass = str_replace('.','_',$row['path']);

                                    $listIndexClass = '';
                                    if ($listOrIndex == 1) $listIndexClass = $group.'_list_index';

                                    if (trim($labelName) == 'list' || trim($labelName) == 'List') {
                                        $labelName = trans('custom_admin.label_role_list');
                                    } else if (trim($labelName) == 'add' || trim($labelName) == 'Add') {
                                        $labelName = trans('custom_admin.label_role_add');
                                    } else if (trim($labelName) == 'edit' || trim($labelName) == 'Edit') {
                                        $labelName = trans('custom_admin.label_role_edit');
                                    } else if (trim($labelName) == 'change status' || trim($labelName) == 'Change Status') {
                                        $labelName = trans('custom_admin.label_role_change_status');
                                    } else if (trim($labelName) == 'delete' || trim($labelName) == 'Delete') {
                                        $labelName = trans('custom_admin.label_role_delete');
                                    } else if (trim($labelName) == 'sort' || trim($labelName) == 'Sort') {
                                        $labelName = trans('custom_admin.label_role_sort');
                                    } else if (trim($labelName) == 'sort product' || trim($labelName) == 'Sort Product') {
                                        $labelName = trans('custom_admin.label_role_sort_product');
                                    } else if (trim($labelName) == 'invoice' || trim($labelName) == 'Invoice') {
                                        $labelName = trans('custom_admin.label_role_invoice');
                                    } else if (trim($labelName) == 'invoice print' || trim($labelName) == 'Invoice Print') {
                                        $labelName = trans('custom_admin.label_role_invoice_print');
                                    } else if (trim($labelName) == 'live order list' || trim($labelName) == 'Live Order List') {
                                        $labelName = trans('custom_admin.label_role_live_order_list');
                                    } else if (trim($labelName) == 'export to excel' || trim($labelName) == 'Export To Excel') {
                                        $labelName = trans('custom_admin.label_export_to_excel');
                                    }  else if (trim($labelName) == 'export to pdf' || trim($labelName) == 'Export To Pdf') {
                                        $labelName = trans('custom_admin.label_export_to_pdf');
                                    }
                                    @endphp
                                    <li>
                                        <input type="checkbox" name="role_page_ids[]" value="{{$row['role_page_id']}}" data-page="{{ $group }}" data-path="{{ $row['path'] }}" data-class="{{ $groupClass }}" data-listIndex="{{$listIndexClass}}" class="setPermission {{ $groupClass }} {{ $subClass }} selectDeselectAll"> <span>{{ $labelName }}</span>
                                    </li>
                                    @php $listOrIndex++; @endphp
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @endif
                    </div>
                    <!-- Permission section end -->
                </div>

                <div class="box-footer">
                    <button type="submit" id="createRole" class="btn btn-primary" title="@lang('custom_admin.btn_submit')">@lang('custom_admin.btn_submit')</button>
                    <a href="{{ route('admin.'.\App::getLocale().'.role.list') }}" class="btn btn-block btn-default btn_width_reset">@lang('custom_admin.btn_cancel')</a>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
$('#createRole').on('click', function(){
    $(this).attr('disabled', true);
    $('#createRoleForm').submit();
});

$(() => {
    //Total checkbox count
    var totalCheckboxCount = $('input[type=checkbox]').length;
    totalCheckboxCount = totalCheckboxCount - 1;
    
    //Top checkbox to select / deselect all "Check" boxes
    $('.mainSelectDeselectAll').click(function(){
        if ($(this).prop('checked') == true) {
            $(".selectDeselectAll").prop('checked', true);
        } else {
            $(".selectDeselectAll").prop('checked', false);
        }
    });

    //Individual section select / deselect
    $('.select_deselect').click(function(){
        var parentRoute = $(this).data('parentroute');
        if ($(this).prop('checked') == true) {
            $("."+parentRoute).prop('checked', true);

            //If total checkbox (except top checkbox) == all checked checkbox then "Check" the Top checkbox
            var totalCheckedCheckbox = $('input[type=checkbox]:checked').length;
            if (totalCheckedCheckbox == totalCheckboxCount) {
                $('.mainSelectDeselectAll').prop('checked', true);
            }
        } else {
            $("."+parentRoute).prop('checked', false);

            //Top checkbox un-check
            $('.mainSelectDeselectAll').prop('checked', false);
        }
    });

    //Particular child checkbox select / deselect
    $(".setPermission").click(function() {
        var routeClass = $(this).data('class');
        var listIndex = $(this).data('listindex');
        var individualSectionCheckboxCount = $('.'+routeClass).length;
        
        if ($(this).prop('checked') == true) {
            //List/Index checkbox "Check"
            $(this).parents('div.section_class').find('input[type=checkbox]:eq(0)').prop('checked', true);

            var childCheckedCheckboxCount = $('.'+routeClass+':checked').length;
            
            //If child checked checkbox count = total checkbox count under individual section
            if (childCheckedCheckboxCount === individualSectionCheckboxCount) {
                //Individual section checkbox "Check"
                $(this).parents('div.individual_section').find('input[type=checkbox]:eq(0)').prop('checked', true);
            }

            //If Total checkbox count == Total checked checkbox count then "Check" the Top checkbox
            if ( ($('input[type=checkbox]').length - 1) == $('input[type=checkbox]:checked').length) {
                $('.mainSelectDeselectAll').prop('checked', true);
            }
        } else {
            //List/index checkbox un-check then "un-check" all child checkbox
            if (listIndex == routeClass+'_list_index') {
                $('.'+routeClass).prop('checked', false);                
            }
            
            //Individual section checkbox "un-check"
            $(this).parents('div.individual_section').find('input[type=checkbox]:eq(0)').prop('checked', false);

            //Top checkbox "un-check"
            $('.mainSelectDeselectAll').prop('checked', false);
        }
    });
});
</script>

@endsection