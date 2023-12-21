@extends('site.layouts.app', [])
  	@section('content')

	@php $siteSetting = Helper::getSiteSettings(); @endphp
        
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="form_wrap form_box text-center">
					@include('site.elements.notification')
					
					<h1 class="heading heading_large">@lang('custom.label_create_account')</h1>
					
					{{ Form::open(array(
									'method'=> 'POST',
									'class' => '',
									'route' => ['site.'.\App::getLocale().'.users.register'],
									'name'  => 'registrationForm',
									'id'    => 'registrationForm',
									'files' => true,
									'autocomplete' => false,
									'novalidate' => true)) }}
						<ul class="row">
							<li class="col-sm-6">
								<div class="form-group">
									<label class="labelWrap">
										<span>@lang('custom.label_first_name') *</span>
										{{ Form::text('first_name', null, array(
																			'id' => 'first_name',
																			'class'=>'text-only',
																			'placeholder' => trans('custom.placeholder_first_name').' *',
																		)) }}
									</label>
									<div class="help_block"></div>
								</div>
								<div class="form-group">
									<label class="labelWrap">
										<span>@lang('custom.label_last_name') *</span>
										{{ Form::text('last_name', null, array(
																			'id' => 'last_name',
																			'class'=>'text-only',
																			'placeholder' => trans('custom.placeholder_last_name').' *',
																		)) }}
									</label>
									<div class="help_block"></div>
								</div>
								<div class="form-group">
									<label class="labelWrap">
										<span>@lang('custom.label_email') *</span>
										{{ Form::email('email', null, array(
																			'id' => 'email',
																			'placeholder' => trans('custom.placeholder_email').' *',
																		)) }}
									</label>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-sm-6">
								<div class="form-group">
									<label class="labelWrap showPass">
										<span>@lang('custom.label_create_password') *</span>
										{{ Form::password('password', array(
																			'id' => 'password',
																			'placeholder' => trans('custom.placeholder_password').' *',
																		)) }}
										<i class="showPassIcon"></i>
									</label>
									<div class="help_block"></div>
								</div>
								<div class="form-group">
									<label class="labelWrap showPass">
										<span>@lang('custom.label_confirm_password') *</span>
										{{ Form::password('confirm_password', array(
																			'id' => 'confirm_password',
																			'placeholder' => trans('custom.placeholder_confirm_password').' *',
																		)) }}
										<i class="showPassIcon"></i>
									</label>
									<div class="help_block"></div>
								</div>
								<div class="form-group">
									<div class="labelWrap">
										<label class="input_check">
										    <input  name="agree" type="checkbox">
											<span>@lang('custom.label_accept') <a href="#">{{ trans('custom.label_terms', ['websiteTitle' => $siteSetting->website_title]) }}</a> @lang('custom.message_terms').</span>
										</label>
									</div>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-sm-12">
								<div class="form-group">
									<div class="labelWrap">
										{!! app('captcha')->display() !!}
									</div>
									<div class="error" id="recaptcha-error"></div>
								</div>
							</li>
							<li class="col-sm-12">
								<button type="submit" class="btn btn-width">@lang('custom.lab_sign_up')</button>
							</li>
							<li class="col-sm-12">
								<div>@lang('custom.label_already_have_an_account') <a href="{{route('site.'.\App::getLocale().'.users.login')}}"><strong>@lang('custom.lab_login')</strong></a></div>
							</li>
						</ul>
					{{ Form::close() }}
				</div>
			</div>
		</section>
	</main>

	@endsection