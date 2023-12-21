@extends('site.layouts.app', [])
  	@section('content')
        
	  <main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')
						
						<h1 class="heading heading_medium b-b">@lang('custom.lab_notifications')</h1>
						<div class="row">
							<div class="col-md-6 col-sm-4 col-xs-12 pull-right">
								<figure class="text-center"><img src="{{asset('images/site/notifacation.gif')}}" alt="@lang('custom.lab_notifications')"></figure>
							</div>
							<div class="col-md-6 col-sm-8 col-xs-12">
								<div class="form_wrap">
									{{ Form::open(array(
													'method'=> 'POST',
													'class' => '',
													'route' => ['site.'.\App::getLocale().'.users.notifications'],
													'name'  => 'notificationsForm',
													'id'    => 'notificationsForm',
													'files' => true,
													'autocomplete' => false,
													'novalidate' => true)) }}
										<ul class="row">
											<li class="col-sm-12">
												<div class="notifacationGroup">
													<h3 class="heading">@lang('custom.label_email')</h3>
													<div class="form-group">
														<span class="tt_fleft">@lang('custom.label_order_update')</span>
														<label class="tt_switch tt_fright">
															<input type="checkbox" @if(isset($notificationDetails->order_update) && $notificationDetails->order_update == '1') checked @endif name="order_update" id="order_update">
															<span class="switchSlider"></span>
														</label>
													</div>
													<div class="form-group">
														<span class="tt_fleft">@lang('custom.label_rate_your_meal')</span>
														<label class="tt_switch tt_fright">
															<input type="checkbox" @if(isset($notificationDetails->rate_your_meal) && $notificationDetails->rate_your_meal == '1') checked @endif name="rate_your_meal" id="rate_your_meal">
															<span class="switchSlider"></span>
														</label>
													</div>
													{{-- <div class="form-group">
														<span class="tt_fleft">@lang('custom.label_sms')</span>
														<label class="tt_switch tt_fright">
															<input type="checkbox" @if(isset($notificationDetails->sms) && $notificationDetails->sms == '1') checked @endif name="sms" id="sms">
															<span class="switchSlider"></span>
														</label>
													</div> --}}
												</div>												
											</li>
											<li class="col-sm-12 text-center">
												<button type="submit" class="btn btn-width">@lang('custom.save_changes')</button>
											</li>
										</ul>
									</form>
								</div>
							</div>
						</div>
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection