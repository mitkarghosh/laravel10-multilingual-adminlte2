@extends('site.layouts.app', [])
  	@section('content')
        
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="form_wrap form_box text-center">
					<h1 class="heading heading_large">@lang('custom.forgot_password')?</h1>
					<p class="text-left">@lang('custom.forgot_password_text')</p>

					@include('site.elements.notification')
					
					{{ Form::open(array(
									'method'=> 'POST',
									'class' => '',
									'route' => ['site.'.\App::getLocale().'.users.forgot-password'],
									'name'  => 'forgetPasswordForm',
									'id'    => 'forgetPasswordForm',
									'files' => true,
									'autocomplete' => false,
									'novalidate' => true)) }}
						<ul class="row">
							<li class="col-sm-12">
								<div class="form-group">
									<label class="labelWrap">
										<span>@lang('custom.label_email') *</span>
										<input type="text" name="email" id="email" placeholder="@lang('custom.placeholder_email') *">
									</label>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-sm-12">
								<button type="submit" class="btn btn-width">@lang('custom.label_reset_password')</button>
							</li>
						</ul>
					{{ Form::close() }}
				</div>
			</div>
		</section>
	</main>

	@endsection