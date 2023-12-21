@extends('site.layouts.app', [])
  	@section('content')
        
	  <main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="form_wrap form_box text-center">
					<h1 class="heading heading_large">@lang('custom.lab_login')</h1>

					@include('site.elements.notification')
					
					{{ Form::open(array(
									'method'=> 'POST',
									'class' => '',
									'route' => ['site.'.\App::getLocale().'.users.login'],
									'name'  => 'loginForm',
									'id'    => 'loginForm',
									'files' => true,
									'autocomplete' => false,
									'novalidate' => true)) }}
						<ul class="row">
							<li class="col-xs-12">
								<div class="form-group">
									<label class="labelWrap">
										<span>@lang('custom.label_email') *</span>
										<input type="email" name="email" id="email" value="" placeholder="@lang('custom.placeholder_email') *">
									</label>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-xs-12">
								<div class="form-group">
									<label class="labelWrap showPass">
										<span>@lang('custom.label_password') *</span>
										<input type="password" name="password" id="password" value="" placeholder="@lang('custom.placeholder_password') *">
										<i class="showPassIcon"></i>
									</label>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-xs-6">
								<div class="form-group">
									<div class="labelWrap">
										<label class="input_check">
											<input type="checkbox" name="">
											<span>@lang('custom.label_stay_signed_in')</span>
										</label>
									</div>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-xs-6">
								<div class="form-group text-right lh30">
									<a href="{{route('site.'.\App::getLocale().'.users.forgot-password')}}"><strong>@lang('custom.forgot_password')?</strong></a>
								</div>
							</li>
							<li class="col-xs-12">
								<button type="submit" class="btn btn-width">@lang('custom.lab_login')</button>
							</li>
							<li class="col-xs-12">
								<div>@lang('custom.label_dont_have_an_account') <a href="{{route('site.'.\App::getLocale().'.users.register')}}"><strong>@lang('custom.lab_sign_up')</strong></a></div>
							</li>
						</ul>
					</form>
				</div>
			</div>
		</section>
	</main>

	@endsection