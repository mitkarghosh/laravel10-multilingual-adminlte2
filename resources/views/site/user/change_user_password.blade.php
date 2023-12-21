@extends('site.layouts.app', [])
	@section('content')
	  
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')
						
						<h1 class="heading heading_medium b-b">@lang('custom.lab_change_password')</h1>
						<div class="row">
							<div class="col-md-6 col-sm-4 col-xs-12 pull-right">
								<figure class="text-center"><img src="{{asset('images/site/change-password.gif')}}" alt="@lang('trans.lab_change_password')"></figure>
							</div>
							<div class="col-md-6 col-sm-8 col-xs-12">
								<div class="form_wrap">
									{{ Form::open(array(
													'method'=> 'POST',
													'class' => '',
													'route' => ['site.'.\App::getLocale().'.users.change-user-password'],
													'name'  => 'changeUserPasswordForm',
													'id'    => 'changeUserPasswordForm',
													'files' => true,
													'autocomplete' => false,
													'novalidate' => true)) }}
										<ul class="row">
											<li class="col-sm-12">
												<div class="form-group">
													<label class="labelWrap showPass">
														<span>@lang('custom.lab_current_password') *</span>
														<input type="password" placeholder="@lang('custom.lab_current_password') *" name="current_password" id="current_password">
														<i class="showPassIcon"></i>
													</label>
													<div class="help_block"></div>
												</div>
											</li>
											<li class="col-sm-12">
												<div class="form-group">
													<label class="labelWrap showPass">
														<span>@lang('custom.lab_new_password') *</span>
														<input type="password" placeholder="@lang('custom.lab_new_password') *" name="password" id="password">
														<i class="showPassIcon"></i>
													</label>
													<div class="help_block"></div>
												</div>
											</li>
											<li class="col-sm-12">
												<div class="form-group">
													<label class="labelWrap showPass">
														<span>@lang('custom.lab_confirm_new_password') *</span>
														<input type="password" placeholder="@lang('custom.lab_confirm_new_password') *" name="confirm_password" id="confirm_password">
														<i class="showPassIcon"></i>
													</label>
													<div class="help_block"></div>
												</div>
											</li>
											<li class="col-sm-12 text-center">
												<button type="submit" class="btn btn-width">@lang('custom.update')</button>
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