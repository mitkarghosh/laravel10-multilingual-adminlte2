@extends('site.layouts.app', [])
	@section('content')

	<main class="mainContainer">
		@include('site.elements.banner_logo')
		
		<section class="section">
			<div class="container">
				<div class="help_details">
					<h1 class="heading">{{$helpDetails->local[0]->local_title}}</h1>
					{!!$helpDetails->local[0]->local_description!!}

					<div class="help_back"><a href="{{route('site.'.\App::getLocale().'.help')}}">@lang('custom.label_faq_home')</a></div>
				</div>
			</div>
		</section>
	</main>

	@endsection