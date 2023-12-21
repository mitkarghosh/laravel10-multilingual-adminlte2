@extends('site.layouts.app', [])
	@section('content')

	<main class="mainContainer">
		@include('site.elements.banner_logo')
		
		<section class="section">
			<div class="container">
				<div class="help_details">
					<h1 class="heading">{{$cmsData['title']}}</h1>
					{!!$cmsData['local_description']!!}
				</div>
			</div>
		</section>
	</main>

	@endsection