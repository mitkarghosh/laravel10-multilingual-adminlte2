@extends('admin.layouts.app', ['title' => $panel_title])

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="live_order" id="live-order">
		{{ $page_title }}		
	</h1>
	<small>
		@lang('custom_admin.label_last_updated'): <span id="last_updated">{{date('d/m/Y H:i:s')}}</span>
	</small>
	<br><br>
	<div class="row">
		<div class="col-md-6"> 
		<?php if ($siteSettings['is_shop_close'] == 'Y') { ?>
			<label for="IsShopClose">@lang('custom_admin.label_is_restaurant_close')</label>
			<label class="switch">
				<input type="checkbox" name="is_shop_close" class="close_availability_featured" data-type="shop" id="is_shop_close" value="Y" checked>
				<span class="slider round"></span>
			</label>
		<?php } else { ?>
			<label for="IsShopClose">@lang('custom_admin.label_is_restaurant_open')</label>
			<label class="switch">
				<input type="checkbox" name="is_shop_close" class="close_availability_featured" data-type="shop" id="is_shop_close" value="Y">
				<span class="slider round"></span>
			</label>
		<?php } ?>   
		</div>
		<div class="col-md-6 text-right">

				<?php if ($siteSettings['is_delivery_close'] == 'Y') { ?>
					<label for="IsShopClose">@lang('custom_admin.label_is__delivery_close')</label>
					<label class="switch">
						<input type="checkbox" name="is_shop_close" class="close_availability_featured" data-type="delivery" id="is_shop_close" value="Y" checked>
						<span class="slider round"></span>
					</label>
				<?php } else { ?>
					<label for="IsShopClose">@lang('custom_admin.label_is__delivery_open')</label>
					<label class="switch">
						<input type="checkbox" name="is_shop_close" class="close_availability_featured" data-type="delivery" id="is_shop_close" value="Y">
						<span class="slider round"></span>
					</label>
				<?php } ?>

				<?php if ($siteSettings['is_pickup_close'] == 'Y') { ?>
					<label for="IsShopClose">@lang('custom_admin.label_is__pickup_close')</label>
					<label class="switch">
						<input type="checkbox" name="is_pickup_close" class="close_availability_featured" data-type="pickup" id="is_pickup_close" value="Y" checked>
						<span class="slider round"></span>
					</label>
				<?php } else { ?>
					<label for="IsShopClose">@lang('custom_admin.label_is__pickup_open')</label>
					<label class="switch">
						<input type="checkbox" name="is_pickup_close" class="close_availability_featured" data-type="pickup" id="is_pickup_close" value="Y">
						<span class="slider round"></span>
					</label>
				<?php } ?>


		</div>
	</div>

    <ol class="breadcrumb">
        <li><a href="{{route('admin.'.\App::getLocale().'.dashboard')}}"><i class="fa fa-dashboard"></i> @lang('custom_admin.lab_home')</a></li>
        <li class="active">{{ $page_title }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
				
				<div class="box-body" id="live_order_list">
					@include('admin.elements.notification')
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-center">
								<button type="button" id="get_latest_order_list" class="btn btn-info btn-lg btn_click_here">@lang('custom_admin.btn_click_to_get_latest_order_list')</button>
							</div>
						</div>
					</div>
				</div>
				
				<hr>

				<div class="box-body" id="live_processing_order_list"></div>

            </div>
			<button id="notify-owner" style="visibility: hidden;"></button>
        </div>
    </div>
</section>
<audio id="chatAudio"><source src="{{asset("uploads/notification/notify.ogg")}}" type="audio/ogg"><source src="{{asset('uploads/notification/notify.mp3')}}" type="audio/mpeg"><source src="{{asset('uploads/notification/notify.wav')}}" type="audio/wav"></audio>
<script type="text/javascript">
var site_url="{{ url('/') }}";
$(document).ready(function() {
	// Getting list order data
	$('#get_latest_order_list').on('click', function() {
	        //$('#whole-area').show();
		getList();
	});

	//$('<audio id="chatAudio"><source src="{{asset("uploads/notification/notify.ogg")}}" type="audio/ogg"><source src="{{asset("uploads/notification/notify.mp3")}}" type="audio/mpeg"><source src="{{asset("uploads/notification/notify.wav")}}" type="audio/wav"></audio>').appendTo('body');

	// Notification alert
	$('#notify-owner').on("click", function() {
    	//$('#chatAudio')[0].play();
	});
	  
	$('.close_availability_featured').on('click', function() {
		$('#whole-area').show();
		var updateShopStatus = site_url + '/securepanel/{{\App::getLocale()}}/update-shop-status';	
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: updateShopStatus,
			method: 'POST',
			data: {type:$(this).data('type')},
			cache:false,
			success: function (response) {
				// $('#whole-area').hide();
				 location.reload();
			}
		});
	})



});
var timeoutVar;
function getList() {
	$('#deliveryInModal').hide();
	$('.modal-backdrop').hide();
	$('#whole-area').show();
	var liveOrderList = site_url + '/securepanel/{{\App::getLocale()}}/liveOrders/live-order-list';	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url: liveOrderList,
		method: 'GET',
		data: {},
		cache:false,
		//timeout: 31000,
		success: function (response) {
			// console.log(response);
			$('#whole-area').hide();

			$('#live_order_list').html(response.html);
			$('#live_processing_order_list').html(response.processingHtml);
			if (response.alertStatus == 1) {
				// Notify owner
				//$('#notify-owner').click();
				//$(document).find('#chatAudio')[0].play();
				if(localStorage.getItem('soundplay')==0){
					$(document).find('#chatAudio')[0].play();	
				    localStorage.setItem('soundplay',1);
					//alert();
					suondPlayAuto();
				}
			}else{
				localStorage.setItem('soundplay',0);
			}
			$('#last_updated').html(response.updated_at);
		},
		complete: function() {
			timeoutVar = setTimeout(getList, 30000);		// 30 second
		},
		error: function(){			 
			 localStorage.setItem('autoloadonfail',1);
			location.href=site_url+'/securepanel/{{\App::getLocale()}}/liveOrders/live-orders';
		}
	});
}


localStorage.setItem('soundplay',0);
function suondPlayAuto(){
	setInterval(function () {
		//console.log('vinod'); 
		if($(document).find('#live_order_list tr').length>1 && localStorage.getItem('soundplay')==1){
			$(document).find('#chatAudio')[0].play();	
		}
		$('#whole-area').hide();
	}, 31000);
}


window.addEventListener("load", function(){
	if(localStorage.getItem('autoloadonfail')=='1'){
		getList();
		localStorage.removeItem('autoloadonfail');	
	}
})


function generateList() {
	setTimeout(function() {
		$('#whole-area').show();
		var liveOrderList = site_url + '/securepanel/{{\App::getLocale()}}/liveOrders/live-order-list';	
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url: liveOrderList,
			method: 'GET',
			data: {},
			success: function (response) {
				// console.log(response.html);
				$('#whole-area').hide();

				$('#live_order_list').html(response.html);
				$('#live_processing_order_list').html(response.processingHtml);

				$('#last_updated').html(response.updated_at);
			}
		});
	}, 2000);
}

$(document).ready(function() {
	setTimeout(function(){
	    window.location.href = '?update_page=1'
	  },1800000);

	var is_update_page = '{{ $_GET["update_page"] ?? "" }}';
	if(is_update_page == 1) {
		getList();
	}
});

</script>

@endsection