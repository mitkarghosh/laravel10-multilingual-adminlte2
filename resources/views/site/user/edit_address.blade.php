@extends('site.layouts.app', [])
	@section('content')
	  
	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')
						
						<h1 class="heading heading_medium b-b">@lang('custom.label_edit_address')</h1>
						<div class="form_wrap">
							{{ Form::open(array(
											'method'=> 'POST',
											'class' => '',
											'route' => ['site.'.\App::getLocale().'.users.edit-address', $id],
											'name'  => 'editAddressForm',
											'id'    => 'editAddressForm',
											'files' => true,
											'autocomplete' => false,
											'novalidate' => true)) }}
								<input type="hidden" name="address_id" id="address_id" value="{{$id}}">
								<ul class="row">
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_company')</span>
												<input type="text" placeholder="@lang('custom.label_company')" name="company" id="company" value="{{$addressDetails->company}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_street') *</span>
												<input type="text" placeholder="@lang('custom.label_street')" name="street" id="street" value="{{$addressDetails->street}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_floor')</span>
												<input type="text" placeholder="@lang('custom.label_floor')" name="floor" id="floor" value="{{$addressDetails->floor}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_door_code')</span>
												<input type="text" placeholder="@lang('custom.label_door_code')" name="door_code" id="door_code" value="{{$addressDetails->door_code}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_postcode') *</span>
												<input type="text" placeholder="@lang('custom.label_postcode')" name="post_code" id="post_code" value="{{$addressDetails->post_code}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<label class="labelWrap1">
												<span>@lang('custom.label_city') *</span>
												<input type="text" placeholder="@lang('custom.label_city')" name="city" id="city" value="{{$addressDetails->city}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-12">
										<div class="form-group">
											<div class="labelWrap1">
												<span>@lang('custom.message_choose_address')</span>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="H" class="custom_click" @if ($addressDetails->alias_type == 'H') checked @endif>
													<span>@lang('custom.label_address_home')</span>
												</label>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="O" class="custom_click" @if ($addressDetails->alias_type == 'O') checked @endif>
													<span>@lang('custom.label_address_office')</span>
												</label>
												<label class="input_radio">
													<input type="radio" name="addressAlias" value="Ot" class="customAlias custom_click" @if ($addressDetails->alias_type == 'Ot') checked @endif>
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
												<input type="text" placeholder="Custom alias" name="customAlias" id="other_address_type" value="{{$addressDetails->own_alias}}" @if ($addressDetails->alias_type != 'Ot') disabled @endif>
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