@extends('site.layouts.app', [])
  	@section('content')
        
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="form_wrap form_box text-center">
					<h1 class="heading heading_large">@lang('custom.reset_password')</h1>
					<p class="text-left">@lang('custom.reset_password_text')</p>

					@include('site.elements.notification')
					
					{{ Form::open(array(
									'method'=> 'POST',
									'class' => '',
									'route' => ['site.'.\App::getLocale().'.users.reset-password', $token],
									'name'  => 'resetPasswordForm',
									'id'    => 'resetPasswordForm',
									'files' => true,
									'autocomplete' => false,
									'novalidate' => true)) }}
						<ul class="row">
							<li class="col-sm-12">
								<div class="form-group">
									<label class="labelWrap showPass">
										<span>@lang('custom.label_password') *</span>
										<input type="password" name="password" id="password" value="" placeholder="@lang('custom.placeholder_password') *">
										<i class="showPassIcon"></i>
									</label>
									<div class="help_block"></div>
								</div>
							</li>
							<li class="col-sm-12">
								<div class="form-group">
									<label class="labelWrap showPass">
										<span>@lang('custom.label_confirm_password') *</span>
										<input type="password" name="confirm_password" id="confirm_password" value="" placeholder="@lang('custom.placeholder_confirm_password') *">
										<i class="showPassIcon"></i>
									</label>
									<div class="help_block"></div>
								</div>
								
							</li>
							<li class="col-sm-12">
								<button type="submit" class="btn btn-width">@lang('custom.submit')</button>
							</li>
						</ul>
					{{ Form::close() }}
				</div>
			</div>
		</section>
	</main>

	@endsection