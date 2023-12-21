@extends('site.layouts.app', [])
	@section('content')

	<main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')
						
						<h1 class="heading heading_medium b-b">@lang('custom.lab_personal_details')</h1>
						<div class="form_wrap">
							{{ Form::open(array(
											'method'=> 'POST',
											'class' => 'profile-update-form',
											'route' => ['site.'.\App::getLocale().'.users.personal-details'],
											'name'  => 'personalDetails',
											'id'    => 'personalDetails',
											'files' => true,
											'autocomplete' => false,
											'novalidate' => true)) }}
								<ul class="row">
									<li class="col-sm-12">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="change_avatar" data-toggle="tt_modal" data-target="#avatar_modal">
													@php
													$userAvatar = URL:: asset('images/site/sample/avatar5.jpg');
													if (Auth::user()->avatarDetails != null) {
														if (file_exists(public_path('/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image))) {
															$userAvatar = URL::to('/').'/uploads/avatar/thumbs/'.Auth::user()->avatarDetails->image;
														}
													}
													@endphp
														<figure class="avatar avatar_update" style="background-image: url({{$userAvatar}});"></figure>
														<span>@lang('custom.lab_change_avatar')</span>
													</div>
												</div>
											</div>
										</div>
									</li>
									<li class="col-sm-6">
										<div class="form-group">
											<label class="labelWrap">
												<span>@lang('custom.lab_nickname')</span>
												<input type="text" placeholder="@lang('custom.lab_nickname')" name="nickname" id="nickname" value="{{$userDetail->nickname}}">
											</label>
											<div class="help_block">@lang('custom.message_nickname')</div>
										</div>
										<div class="form-group">
											<label class="labelWrap">
												<span>@lang('custom.lab_title')</span>
												<select name="title" id="title">
													<option value="">-</option>
													<option value="Mr" @if($userDetail->title == 'Mr') selected @endif>@lang('custom.lab_title_mr')</option>
													<option value="Mrs" @if($userDetail->title == 'Mrs') selected @endif>@lang('custom.lab_title_mrs')</option>
													<!-- <option value="Miss" @if($userDetail->title == 'Miss') selected @endif>@lang('custom.lab_title_miss')</option> -->
												</select>
											</label>
											<div class="help_block">@lang('custom.message_title')</div>
										</div>
										<div class="form-group">
											<label class="labelWrap">
												<span>@lang('custom.label_first_name') *</span>
												<input type="text" placeholder="@lang('custom.placeholder_first_name') *" name="first_name" id="first_name" value="{{$userDetail->first_name}}">
											</label>
											<div class="help_block"></div>
										</div>
										<div class="form-group">
											<label class="labelWrap">
												<span>@lang('custom.label_last_name') *</span>
												<input type="text" placeholder="@lang('custom.placeholder_last_name') *" name="last_name" id="last_name" value="{{$userDetail->last_name}}">
											</label>
											<div class="help_block"></div>
										</div>
									</li>
									<li class="col-sm-6">
										<div class="form-group">
											<label class="labelWrap iconLabelWrap">
												<span>@lang('custom.lab_language')</span>
												<select name="login_language" id="login_language">
													<option value="en" @if($userDetail->login_language == 'en') selected @endif>@lang('custom.lang_english')</option>
													<option value="de" @if($userDetail->login_language == 'de') selected @endif>@lang('custom.lang_german')</option>
												</select>
												<i class="icon-speech"></i>
											</label>
											<div class="help_block"></div>
										</div>
										<div class="form-group">
											<label class="labelWrap">
												<span>@lang('custom.label_email') *</span>
												<input type="text" placeholder="@lang('custom.placeholder_email') *" name="email" id="email" value="{{$userDetail->email}}">
											</label>
											<div class="help_block"></div>
										</div>
										<div class="form-group">
											<label class="labelWrap ">{{--contactNo--}}
												<span>@lang('custom.label_contact_number') *</span>
												{{--<em>+49</em>--}}
												<input type="text" placeholder="@lang('custom.placeholder_contact_number') *" name="phone_no" id="phone_no" value="{{$userDetail->phone_no}}">
											</label>
											<div class="help_block">@lang('custom.message_phone_number')</div>
										</div>
										<div class="form-group">
											<label class="labelWrap dateWrap">
												<span>@lang('custom.label_date_of_birth')</span>
												<input type="text" placeholder="@lang('custom.label_date_of_birth')" name="dob" id="dob" class="dob_datepicker" value="{{date('d/m/Y',strtotime($userDetail->dob))}}">
											</label>
											<div class="help_block">@lang('custom.message_dob')</div>
										</div>
									</li>
									<li class="col-sm-12 text-center">
										<button type="submit" class="btn btn-width">@lang('custom.update')</button>
									</li>
								</ul>
							</form>
						</div>
					</article>
				</div>
			</div>
		</section>
	</main>

	<div class="tt_modal text-center" id="avatar_modal">
        <div class="tt_modal_container">
            <div class="tt_modal_main">
                <span class="tt_modal_close ti-close" data-dismiss="tt_modal"></span>
                <div class="tt_modal_header">@lang('custom.label_choose_your_avatar')</div>
                <div class="tt_modal_body">
                    <div class="avatar_list">
					@if ($avatarList->count() > 0)
                        <p>@lang('custom.message_choose_your_avatar')</p>
                        <ul>
						@foreach ($avatarList as $avatar)
							@php
							$avatarImg = URL:: asset('images').'/site/'.Helper::NO_IMAGE;
							if (file_exists(public_path('/uploads/avatar/thumbs/'.$avatar->image))) {
								$avatarImg = URL::to('/').'/uploads/avatar/thumbs/'.$avatar->image;
							}
							$avatarid = 0;
							if (Auth::user()->avatar_id != null) {
								$avatarid = Auth::user()->avatar_id;
							}
							@endphp
                            <li @if($avatarid == $avatar->id) class="selected" @endif>
                                <div class="select_avatar update_avatar" data-id="{{$avatar->id}}">
                                    <figure class="avatar" style="background-image: url({{$avatarImg}});"></figure>
									<span>{{$avatar->local[0]->local_title}}</span>
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
        </div>
    </div>

	@endsection