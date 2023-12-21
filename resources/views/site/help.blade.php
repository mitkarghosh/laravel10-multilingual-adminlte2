@extends('site.layouts.app', [])
	@section('content')

	<main class="mainContainer">
		@include('site.elements.banner_logo')
	
		<section class="section help_page">
			<div class="container">
				<div class="help_section">
					<h1 class="heading">@lang('custom.text_help')</h1>
					<div class="heading_tag">@lang('custom.message_help')</div>
					<div class="help_tag_list">
					@if ($helpList->count() > 0)
						<ul>
						@foreach ($helpList as $list)
							<li><a href="{{route('site.'.\App::getLocale().'.help-details', Helper::customEncryptionDecryption($list->id))}}">{{$list->local[0]->local_title}}</a></li>
						@endforeach
						</ul>
					@endif
					</div>
				</div>

				<div class="help_section">
					<h2 class="heading">@lang('custom.label_faq')</h2>
					<div class="faq_list">
						<h3 class="heading">@lang('custom.label_overview')</h3>
					@if ($faqList->count() > 0)
						<ul class="ul row">
						@foreach ($faqList as $key => $list)
							<li class="col-sm-6"><a href="#faq{{$key}}">{{$key+1}}. {{$list->local[0]->local_title}}</a></li>
						@endforeach
						</ul>
					@endif
					</div>
					<div class="faq_details">
					@foreach ($faqList as $key => $list)
						<div class="faq_ans" id="faq{{$key}}">
							<h3 class="help_title">{{$key+1}}. {{$list->local[0]->local_title}}</h3>
							{!!$list->local[0]->local_description!!}
						</div>
					@endforeach						
					</div>
				</div>

				<div class="help_section">
					<h2 class="heading">@lang('custom.label_contact_support')</h2>
					<div class="heading_tag">@lang('custom.message_contact_support')</div>
					<div class="row">
						<div class="col-md-8 col-sm-7 col-xs-6">
							<div class="contact_box">
								<h3 class="help_title">@lang('custom.message_reach_us'):</h3>
								{!! $cmsData['local_description'] !!}
							</div>
						</div>
						<div class="col-md-4 col-sm-5 col-xs-6">
							<div class="contact_box">
								<h3 class="help_title">Post:</h3>
								<p>
									{{str_replace('<br>','',$siteSettings->address)}}
								</p>
							</div>
						</div>
					</div>
				</div>				
			</div>
		</section>
	</main>

	@endsection