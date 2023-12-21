@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $page_title }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li><a href="{{route('admin.'.\App::getLocale().'.category.list')}}"><i class="fa fa-book" aria-hidden="true"></i> @lang('custom_admin.lab_category_list')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

@php
$localCategoryTitle = $details->title;
foreach ($details->local as $localDetails) {
    if ($localDetails->lang_code == strtoupper(\App::getLocale())) {
        $localCategoryTitle = $localDetails->local_title;
    }
}
@endphp
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3>@lang('custom_admin.lab_category'): {{$localCategoryTitle}}</h3>
                </div>
                <!-- Search Section End -->

                <div class="box-body table-responsive">
                @if ($list)
                    <p>@lang('custom_admin.message_product_ascending').</p>
                    <div class="well">
                        <div class="dd" id="nestable">                
                            <ol class="dd-list">
                            @foreach ($list as $row)
                                <li class="dd-item nested-list-item is_main_parent" data-id="{{$row->id}}" title="Drag">
                                    <div class="dd-handle nested-list-handle"></div>
                                    <div class="dd-handle grab nested-list-content">{{$row->title}}
                                        <span class="tip-msg"></span>
                                        <div class="float-right"><span class="tip-hide"></span>
                                            <span class="tip-hide text-info date_enabled"></span>
                                            <span class="tip-hide text-success date_enabled"></span>                                            
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            </ol>    
                        </div>
                    </div>            
                @else            
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2">@lang('custom_admin.lab_no_records_found')</td>
                        </tr>       
                    </table>
                @endif
                </div>
                <!-- /.box-body -->
            </div>        
        </div>
    </div>
</section>
<!-- /.content -->

<script type="text/javascript">
    $(function() {
        // Process on drag
        $('.dd').nestable({
            maxDepth: 1,
            dropCallback: function(details) {    
                var order = new Array();
                $("li[data-id='"+details.destId +"']").find('ol:first').children().each(function(index,elem) {
                    order[index] = $(elem).attr('data-id');
                });
                    
                if (order.length === 0) {
                    var order = new Array();
                    $("#nestable > ol > li").each(function(index,elem) {
                        order[index] = $(elem).attr('data-id');
                    });
                }
    
                // don't post if nothing changed
                var data_id = window.location.hostname + '.nestable_Multi Kitchen';
                var drag_data = JSON.stringify($('.dd').nestable('serialize'));
                var storage_data = localStorage.getItem(data_id);
                if (drag_data === storage_data) {
                    return false;
                } else {
                    $('#whole-area').show();
                    localStorage.setItem(data_id, drag_data);
    
                    // post data by ajax
                    $.ajax({
                        url: '{{ route("admin.".\App::getLocale().".category.save-sort-product") }}',
                        type: 'post',
                        data: {
                            sourceId : details.sourceId,
                            destinationId: details.destId,
                            order: order
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (response) {
                            $('#whole-area').hide();
                            // $("li[data-id='"+ details.sourceId +"']")
                            //             .find(".tip-msg")
                            //             .first()
                            //             .html(response.message)
                            //             .fadeIn(100)
                            //             .delay(1000)
                            //             .fadeOut();
                            Swal.fire({
                                title: '{{trans("custom_admin.message_success")}}',
                                text: response.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok'
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: '{{trans("custom_admin.message_error")}}',
                                text: response.message,
                                icon: 'error',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok'
                            });

                            // alert("Failed: " + response.status + "：" + response.message);
                            return ;
                        }
                    });
                }
                
            }
        }).nestable('collapseAll');    
    });
    </script>

@endsection