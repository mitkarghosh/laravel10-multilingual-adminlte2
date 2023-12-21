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
					
					<div class="tt_map_wrap">
						<div class="tt_map">
							{!!$getSiteSettingData->map!!}
						</div>
						<div class="tt_map_address">
							<h1 class="heading">@lang('custom.label_address')</h1>
							<p>{{str_replace('<br>','',$getSiteSettingData->address)}}</p>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6 mt15 pr0">
							<div class="tt_box">
								{!!$cmsData['local_description']!!}
								
								<h2 class="heading">@lang('custom.label_delivery_areas')</h2>
								<div class="area_list">
								@if ($pinCodeList->count() > 0)
									<ul class="ul row">
									@foreach ($pinCodeList as $area)
										<li class="col-sm-6">
											<a href="#">
												{{$area->code.' '.$area->area}}
												@if ($area->minimum_order_amount > 0)
												{!! ' '.$area->minimum_order_amount.' '.env('WEBSITE_CURRENCY') !!}
												@endif
											</a>
										</li>
									@endforeach
									</ul>
								@else
									<p>@lang('custom.message_no_records_found')</p>
								@endif
								</div>
							</div>
						</div>
						<div class="col-sm-6 mt15 pl0">
							<div class="tt_box delivery_time">
								<h2 class="heading">@lang('custom.lab_delivery')</h2>
							@if ($availableList->count() > 0)
								<ul class="ul">
									@php $curDate = $firstDate = date('Y-m-d'); @endphp
								@foreach ($availableList as $item)
									@php
									
									$specialHour	= Helper::specialHourCalculation($curDate);
									if ($item->day_title == date('l')) {
										$curDate = date('Y-m-d', strtotime($curDate . ' +1 day'));
									} else if (strtotime($curDate) > strtotime($firstDate)) {
										$curDate = date('Y-m-d', strtotime($curDate . ' +1 day'));
									}
									@endphp
									<li @if ($item->day_title == date('l')) class="active" @endif>
										<div class="tt_fleft">
										@if (App::getLocale() == 'en')
											{{$item->day_title}}
										@else
											{{$item->day_title_de}}
										@endif
										</div>
										<div class="tt_fright">
								@if ($specialHour == null)
									@if ($item->holiday == 0)
									   
									 @foreach(Helper::getSloatByDayId($item->id) as $list)
                                          <div>
										  @if ($list->end_time == '23:59:00')
                                                 @php $list->end_time='00:00'; @endphp
										  @endif 
											  {{date('H:i',strtotime($list->start_time))}} - {{date('H:i',strtotime($list->end_time))}}
										  </div>
									 @endforeach
									@else
										<div>@lang('custom.message_holiday')</div>
									@endif
								@else
									@if ($item->day_title != date('l', strtotime($specialHour->special_date)))
										@if ($item->holiday == 0)
											<div>
												{{date('H:i', strtotime($item->start_time))}} - 
											@if ($item->end_time == '23:59:00')
												{{ '00:00' }}
											@else
												{{date('H:i',strtotime($item->end_time))}}
											@endif
											</div>
											@if ($item->start_time2 != null && $item->end_time2 != null)
												<div>
													{{date('H:i', strtotime($item->start_time2))}} - 
												@if ($item->end_time2 == '23:59:00')
													{{ '00:00' }}
												@else
													{{date('H:i',strtotime($item->end_time2))}}
												@endif
												</div>
											@endif
										@else
											<div>@lang('custom.message_holiday')</div>
										@endif
									@elseif ($item->day_title == date('l', strtotime($specialHour->special_date)))
										@if ($specialHour->holiday == 0)
											<div>
												{{date('H:i', strtotime($specialHour->start_time))}} - 
											@if ($specialHour->end_time == '23:59:00')
												{{ '00:00' }}
											@else
												{{date('H:i',strtotime($specialHour->end_time))}}
											@endif
											</div>
											@if ($specialHour->start_time2 != null && $specialHour->end_time2 != null)
												<div>
													{{date('H:i', strtotime($specialHour->start_time2))}} - 
												@if ($specialHour->end_time2 == '23:59:00')
													{{ '00:00' }}
												@else
													{{date('H:i',strtotime($specialHour->end_time2))}}
												@endif
												</div>
											@endif
										@else
											<div>@lang('custom.message_holiday')</div>
										@endif
									@endif
								@endif
										</div>
									</li>
								@endforeach									
								</ul>
							@else
								<p>@lang('custom.message_no_records_found')</p>
							@endif
							</div>
						</div>
					</div>
				</article>

				<aside class="tt_sidebar sidebar_right stickySidebar">
					@include('site.elements.cart_right_panel')
				</aside>
			</div>
		</section>

		@include('site.elements.popups')

	@endsection