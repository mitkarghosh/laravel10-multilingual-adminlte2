@extends('site.layouts.app', [])
	@section('content')
	  
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')
						
						<h1 class="heading heading_medium b-b">@lang('custom.label_add_address')</h1>
						<div class="form_wrap">
							{{ Form::open(array(
											'method'=> 'POST',
											'class' => '',
											'route' => ['site.'.\App::getLocale().'.users.add-address'],
											'name'  => 'addAddressForm',
											'id'    => 'addAddressForm',
											'files' => true,
											'autocomplete' => false,
											'novalidate' => true)) }}
								<ul class="row">
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_company')</span>
												{{ Form::text('company', null, array(
																				'id' => 'company',
																				'placeholder' => trans('custom.label_company'),
																				'class' => 'form-control'
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_street') *</span>
												{{ Form::text('street', null, array(
																				'id' => 'street',
																				'placeholder' => trans('custom.label_street'),
																				'class' => 'form-control',
																				'required' => 'required'
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_floor')</span>
												{{ Form::text('floor', null, array(
																				'id' => 'floor',
																				'placeholder' => trans('custom.label_floor'),
																				'class' => 'form-control',
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_door_code')</span>
												{{ Form::text('door_code', null, array(
																				'id' => 'door_code',
																				'placeholder' => trans('custom.label_door_code'),
																				'class' => 'form-control',
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_postcode') *</span>
												{{ Form::text('post_code', null, array(
																				'id' => 'post_code',
																				'placeholder' => trans('custom.label_postcode'),
																				'class' => 'form-control',
																				'required' => 'required'
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_city') *</span>
												{{ Form::text('city', null, array(
																				'id' => 'city',
																				'placeholder' => trans('custom.label_city'),
																				'class' => 'form-control',
																				'required' => 'required'
																				)) }}
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<div class="labelWrap1">
												<span>@lang('custom.message_choose_address')</span>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="H" class="custom_click" checked>
													<span>@lang('custom.label_address_home')</span>
												</label>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="O" class="custom_click">
													<span>@lang('custom.label_address_office')</span>
												</label>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="Ot" class="customAlias custom_click">
													<span>@lang('custom.label_add_address_alias')</span>
												</label>
											</div>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span class="hideLabel">@lang('custom.label_custom_alias')</span>
												<input type="text" placeholder="Custom alias" name="customAlias" id="other_address_type" disabled>
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12 text-center">
										<button type="submit" class="btn btn-width">@lang('custom.save')</button>
										<div class="mt15 f16"><a href="{{route('site.'.\App::getLocale().'.users.delivery-address')}}"><strong>@lang('custom.cancel')</strong></a></div>
									</li>
								</ul>
							</form>
						</div>
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection