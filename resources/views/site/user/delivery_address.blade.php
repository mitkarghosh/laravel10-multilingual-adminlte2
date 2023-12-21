@extends('site.layouts.app', [])
  	@section('content')
        
	  <main class="mainContainer">
		<section class="section">
			<div class="container">
				<div class="row">
					
					@include('site.elements.side_menu')

					<article class="col-md-9 col-sm-8 stickyContent">
						@include('site.elements.notification')

						<div class="heading heading_medium @if ($deliveryAddresses->count() > 0) b-b @endif clearfix">
							<div class="tt_fleft">@lang('custom.lab_new_delivery_address')</div>
							<a href="{{route('site.'.\App::getLocale().'.users.add-address')}}" class="tt_fright header_link"><i class="plus">+</i> @lang('custom.label_add_address')</a>
						</div>
					
					@if ($deliveryAddresses->count() > 0)
						<div class="address_list">
							<ul class="ul border_list">
							@foreach ($deliveryAddresses as $address)
								@php
								if ($address->alias_type == 'H') {$aliasType = 'H'; $aliasName = trans('custom.label_address_home');}
								else if ($address->alias_type == 'O') {$aliasType = 'O'; $aliasName = trans('custom.label_address_office');}
								else if ($address->alias_type == 'Ot') {$aliasType = substr($address->own_alias, 0, 1);  $aliasName = $address->own_alias;}
								@endphp
								<li id="address_{{$address->id}}">
									<div class="addressBox">
										<div class="addressLink">
											<a href="{{route('site.'.\App::getLocale().'.users.edit-address', Helper::customEncryptionDecryption($address->id))}}"><i class="ti-pencil"></i></a>
											<a href="javascript:void(0);" data-addressid="{{Helper::customEncryptionDecryption($address->id)}}" data-addrid="{{$address->id}}" class="delete_address"><i class="ti-trash"></i></a>
										</div>
										<div class="subheading"><i class="addressIcon">{{strtoupper($aliasType)}}</i> {{$aliasName}}</div>
										<div>{{Auth::user()->title.'. '.Auth::user()->first_name.' '.Auth::user()->last_name}}</div>
										<div>{{$address->street}}</div>
										<div>{{$address->post_code}}, {{$address->city}}</div>
									</div>
								</li>
							@endforeach								
							</ul>
						</div>
					@endif
					</article>
				</div>
			</div>
		</section>
	</main>

	@endsection