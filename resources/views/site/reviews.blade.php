@extends('site.layouts.app', [])
	@section('content')

	@php						
	$getSiteSettingData 				= Helper::getSiteSettings();
	$explodeOverallStarRating 			= explode('.',$getAllReviewDetails['starAvgOverallRating']);
	$explodeFoodQualityStarRating 		= explode('.',$getAllReviewDetails['starAvgFoodDeliveryRating']);
	$explodeDeliveryTimeStarRating 		= explode('.',$getAllReviewDetails['starAvgDeliveryTimeRating']);
	$explodeDriverFriendlinessStarRating= explode('.',$getAllReviewDetails['starAvgDriverFriendlinessRating']);
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

					@php
					
					@endphp
					<div class="tt_box reviewBox">
						<h1 class="heading">@lang('custom.overall')</h1>
						<div class="rating_wrap rating_large">
							<div class="rating">
							@php
							$hafStartFlag = 0;
							for ($k = 1; $k <= $explodeOverallStarRating[0]; $k++)	 {
								echo '<i class="fa fa-star"></i>';
							}
							for ($m = 5 - $explodeOverallStarRating[0]; $m >= 1; $m--)	 {
								if (isset($explodeOverallStarRating[1]) && $explodeOverallStarRating[1] != 0 && $hafStartFlag == 0) {
									echo '<i class="fa fa-star-half-o"></i>';
									$hafStartFlag++;
								} else {
									echo '<i class="fa fa-star-o"></i>';
								}								
							}
							@endphp
							</div>
							<div class="rating_avg"><span>{{$getAllReviewDetails['avgOverallRating']}}</span> / 5</div>
						</div>
						<div class="mt25">
							<h2 class="subheading">Ø @lang('custom.based_on_last_3_months')</h2>
							<p>@lang('custom.message_overall_rating')</p>
							<div class="rating_details">
								<div class="rating_info">
									<ul class="ul">
										<li>
											<h3 class="subheading">@lang('custom.label_food_quality')</h3>
											<div class="rating_wrap ">
												<div class="rating">
												@php
												$foodQualityHafStartFlag = 0;
												for ($b = 1; $b <= $explodeFoodQualityStarRating[0]; $b++)	 {
													echo '<i class="fa fa-star"></i>';
												}
												for ($n = 5 - $explodeFoodQualityStarRating[0]; $n >= 1; $n--)	 {
													if (isset($explodeFoodQualityStarRating[1]) && $explodeFoodQualityStarRating[1] != 0 && $foodQualityHafStartFlag == 0) {
														echo '<i class="fa fa-star-half-o"></i>';
														$foodQualityHafStartFlag++;
													} else {
														echo '<i class="fa fa-star-o"></i>';
													}								
												}
												@endphp
												</div>
												<div class="rating_avg"><span>{{$getAllReviewDetails['avgFoodDeliveryRating']}}</span> / 5</div>
											</div>
										</li>
										<li>
											<h3 class="subheading">@lang('custom.labe_delivery_time')</h3>
											<div class="rating_wrap ">
												<div class="rating">
												@php
												$deliveryTimeHafStartFlag = 0;
												for ($c = 1; $c <= $explodeDeliveryTimeStarRating[0]; $c++)	 {
													echo '<i class="fa fa-star"></i>';
												}
												for ($p = 5 - $explodeDeliveryTimeStarRating[0]; $p >= 1; $p--)	 {
													if (isset($explodeDeliveryTimeStarRating[1]) && $explodeDeliveryTimeStarRating[1] != 0 && $deliveryTimeHafStartFlag == 0) {
														echo '<i class="fa fa-star-half-o"></i>';
														$deliveryTimeHafStartFlag++;
													} else {
														echo '<i class="fa fa-star-o"></i>';
													}								
												}
												@endphp
												</div>
												<div class="rating_avg"><span>{{$getAllReviewDetails['avgDeliveryTimeRating']}}</span> / 5</div>
											</div>
										</li>
										<li>
											<h3 class="subheading">@lang('custom.label_driver_friendliness')</h3>
											<div class="rating_wrap ">
												<div class="rating">
												@php
												$driverFriendlinessHafStartFlag = 0;
												for ($d = 1; $d <= $explodeDriverFriendlinessStarRating[0]; $d++)	 {
													echo '<i class="fa fa-star"></i>';
												}
												for ($q = 5 - $explodeDriverFriendlinessStarRating[0]; $q >= 1; $q--)	 {
													if (isset($explodeDriverFriendlinessStarRating[1]) && $explodeDriverFriendlinessStarRating[1] != 0 && $driverFriendlinessHafStartFlag == 0) {
														echo '<i class="fa fa-star-half-o"></i>';
														$driverFriendlinessHafStartFlag++;
													} else {
														echo '<i class="fa fa-star-o"></i>';
													}								
												}
												@endphp
												</div>
												<div class="rating_avg"><span>{{$getAllReviewDetails['avgDriverFriendlinessRating']}}</span> / 5</div>
											</div>
										</li>
									</ul>
								</div>
								<div class="rating_percent">
									<ul class="ul">
										<li>
											<div class="rating_wrap">
												<div class="rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
												<div class="progressBar rating_progress">
													<span>{{$getAllReviewDetails['total5StarPercent']}}%</span>
													<div class="progressInner" data-percent="{{$getAllReviewDetails['total5StarPercent']}}" style="width:{{$getAllReviewDetails['total5StarPercent']}}%;"></div>
												</div>
											</div>
										</li>
										<li>
											<div class="rating_wrap">
												<div class="rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
												</div>
												<div class="progressBar rating_progress">
													<span>{{$getAllReviewDetails['total4StarPercent']}}%</span>
													<div class="progressInner" data-percent="{{$getAllReviewDetails['total4StarPercent']}}" style="width:{{$getAllReviewDetails['total4StarPercent']}}%;"></div>
												</div>
											</div>
										</li>
										<li>
											<div class="rating_wrap">
												<div class="rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
												</div>
												<div class="progressBar rating_progress">
													<span>{{$getAllReviewDetails['total3StarPercent']}}%</span>
													<div class="progressInner" data-percent="{{$getAllReviewDetails['total3StarPercent']}}" style="width:{{$getAllReviewDetails['total3StarPercent']}}%;"></div>
												</div>
											</div>
										</li>
										<li>
											<div class="rating_wrap">
												<div class="rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
												</div>
												<div class="progressBar rating_progress">
													<span>{{$getAllReviewDetails['total2StarPercent']}}%</span>
													<div class="progressInner" data-percent="{{$getAllReviewDetails['total2StarPercent']}}" style="width:{{$getAllReviewDetails['total2StarPercent']}}%;"></div>
												</div>
											</div>
										</li>
										<li>
											<div class="rating_wrap">
												<div class="rating">
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
													<i class="fa fa-star-o"></i>
												</div>
												<div class="progressBar rating_progress">
													<span>{{$getAllReviewDetails['total1StarPercent']}}%</span>
													<div class="progressInner" data-percent="{{$getAllReviewDetails['total1StarPercent']}}" style="width:{{$getAllReviewDetails['total1StarPercent']}}%;"></div>
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

				@if (count($getAllReviewDetails['userReviewList']) > 0)
					<div class="reviewList">
						<ul class="ul">
						@foreach ($getAllReviewDetails['userReviewList'] as $val)
							@php
							$explodeRating = explode('.',$val['avg_star_rating']);	
							@endphp
							<li>
								<div class="tt_box reviewItem">
									<h2 class="heading">{{$val['first_name']}}</h2>
									<div class="reviewDate">{{date('d.m.Y H:i', strtotime($val['reviewed_on']))}}</div>
									<div class="rating_wrap ">
										<div class="rating">
										@php
										$allHalfStartFlag = 0;
										for ($u = 1; $u <= $explodeRating[0]; $u++)	 {
											echo '<i class="fa fa-star"></i>';
										}
										for ($e = 5 - $explodeRating[0]; $e >= 1; $e--)	 {
											if (isset($explodeRating[1]) && $explodeRating[1] != 0 && $allHalfStartFlag == 0) {
												echo '<i class="fa fa-star-half-o"></i>';
												$allHalfStartFlag++;
											} else {
												echo '<i class="fa fa-star-o"></i>';
											}								
										}
										@endphp
										</div>
									</div>
									<div class="reviewContent">{{$val['short_review']}}</div>
								</div>
							</li>
						@endforeach
						</ul>
					</div>
				@endif
				</article>

				<aside class="tt_sidebar sidebar_right stickySidebar">
					@include('site.elements.cart_right_panel')
				</aside>
			</div>
		</section>

		@include('site.elements.popups')

	@endsection