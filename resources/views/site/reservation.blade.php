@extends('site.layouts.app', [])
	@section('content')

	@php						
	$getSiteSettingData = Helper::getSiteSettings();
	@endphp
	
		@include('site.elements.banner_logo')
		
		@include('site.elements.tab_menu')
	
		<section class="section">
			<div class="container">
				<article class="tt_container no_left_sidebar stickyContent">
					<div class="alert alert-info withIcon">
						<i class="fa fa-exclamation"></i>
						<a href="javascript:void(0);" data-toggle="tt_modal" data-target="#allergy_modal">@lang('custom.text_allergen')</a>
					</div>
					
					<div class="tt_box1">
						{!!$cmsData['local_description']!!}

						@include('site.elements.notification')

						{{ Form::open(array(
										'method'=> 'POST',
										'class' => '',
										'route' => ['site.'.\App::getLocale().'.reservation'],
										'name'  => 'reservationForm',
										'id'    => 'reservationForm',
										'files' => true,
										'autocomplete' => false,
										'novalidate' => true)) }}
							<div class="form_wrap form_box">
								<ul class="row">
									<li class="col-md-4 col-sm-6">
										<div class="form-group">
											<label class="labelWrap iconLabelWrap dateWrap">
												<span>@lang('custom.label_date')</span>
												<input type="text" class="form-control datepicker" name="reservation_date" id="reservation_date" autocomplete="off" placeholder="dd/mm/yyyy">
												<i class="icon-calendar"></i>
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-md-4 col-sm-6">
										<div class="form-group">
											<label class="labelWrap iconLabelWrap clockpicker" data-autoclose="true">
												<span>@lang('custom.label_time')</span>
												<input type="text" class="form-control" name="delivery_time" id="delivery_time" autocomplete="off" placeholder="" value="">
												<i class="icon-clock"></i>
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-md-4 col-sm-12">
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.label_people')</span>
												<input type="text" class="form-control" name="people" id="people" placeholder="">
												<i class="icon-people"></i>
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-md-12 col-sm-12">
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.label_name')</span>
												<input type="text" class="form-control" name="name" id="name" placeholder="">
												<i class="icon-user"></i>
											</label>
											<div class="help_block"></div>
										</div>
										
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.label_email_address')</span>
												<input type="text" class="form-control" name="email" id="email" placeholder="">
												<i class="icon-envelope-open"></i>
											</label>
											<div class="help_block"></div>
										</div>
										
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.label_phone')</span>
												<input type="text" class="form-control" name="phone" id="phone" placeholder="">
												<i class="icon-phone"></i>
											</label>
											<div class="help_block"></div>
										</div>
										
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.label_message')</span>
												<textarea class="form-control" name="message" id="message" placeholdar=""></textarea>
												<i class="icon-speech"></i>
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-md-12 col-sm-12 text-center">
										<button type="submit" class="btn btn-width">@lang('custom.reservation_submit')</button>
									</li>
								</ul>
							</div>
						{{ Form::close() }}
					</div>
				</article>

				<aside class="tt_sidebar sidebar_right stickySidebar">
					@include('site.elements.cart_right_panel')
				</aside>
			</div>
		</section>

		@include('site.elements.popups')

	@endsection