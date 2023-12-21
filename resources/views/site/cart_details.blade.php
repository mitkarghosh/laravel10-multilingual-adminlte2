@php
$langCode = strtoupper(\App::getLocale());
$siteSettingsData = Helper::getSiteSettings();
$loginStatus = 0;
if (Auth::user()) {
	$loginStatus = 1;
}
@endphp
@if (count($cartDetails['itemDetails']) > 0)
	{{-- Cart details --}}
	<div class="table-responsive">
		<table>
			<tbody>
			@foreach ($cartDetails['itemDetails'] as $keyItem => $valIem)
				<tr>
					<td><strong>{{$valIem['quantity']}}x</strong></td>
					<td>
						<strong>{{$valIem['local_details'][$langCode]['local_title']}}</strong>
						{{-- Menu --}}
						@if ($valIem['is_menu'] == 'Y' && count($valIem['menu_title_value_local_details']) > 0)
							<div class="mt5">
								@php
								$menu = 1;
								foreach ($valIem['menu_title_value_local_details'][$langCode] as $keyMenu => $valMenu) {
									// echo $valMenu['menu_local_title'].': '.$valMenu['menu_local_value'];
									if (strpos($valMenu['menu_local_value'], '#') !== false) {
										$explodedValue = explode('#', $valMenu['menu_local_value']);
										$d = 1;
										foreach ($explodedValue as $keyRe => $valueRe) {
											echo '+ '.$valueRe;
											if ($d < count($explodedValue)) {
												echo '<br>';
											}
											$d++;
										}
									} else {
										echo '+ '.$valMenu['menu_local_value'];
									}
									if ($menu < count($valIem['menu_title_value_local_details'][$langCode])) {
										echo '<br>';
									}
									$menu++;
								}
								@endphp
							</div>
						@endif

						{{-- Ingredients --}}
						@if ($valIem['has_ingredients'] == 'Y' && count($valIem['ingredient_local_details']) > 0)
							<div class="mt5">
								@php
								$ingred = 1;
								foreach ($valIem['ingredient_local_details'] as $ingredient) {
									echo $ingredient[$langCode]['local_title'];
									if ($ingred < count($valIem['ingredient_local_details'])) {
										echo ', ';
									}
									$ingred++;
								}
								@endphp
							</div>
						@endif

						{{-- Attributes --}}
						@if ($valIem['has_attribute'] == 'Y')
							<div class="mt5">
								@php
								echo $valIem['attribute_local_details'][$langCode]['local_title'];
								@endphp
							</div>
						@endif
					</td>

					<td class="td_qty">
						<div class="tt_qty">
							<input type="hidden" name="qty" value="1" readonly="" class="tt_qtyInput">

							<a href="javascript: void(0);" class="tt_qtyMinus updateCartItem" data-cart_status="{{Helper::customEncryptionDecryption('decrease')}}" data-order_id="{{Helper::customEncryptionDecryption($valIem['order_id'])}}" data-order_details_id="{{Helper::customEncryptionDecryption($valIem['id'])}}">-</a>
							
							<span class="tt_qtyText">CHF {{$valIem['total_price']}}</span>

							<a href="javascript: void(0);" class="tt_qtyAdd updateCartItem" data-cart_status="{{Helper::customEncryptionDecryption('increase')}}" data-order_id="{{Helper::customEncryptionDecryption($valIem['order_id'])}}" data-order_details_id="{{Helper::customEncryptionDecryption($valIem['id'])}}">+</a>
						</div>
					</td>
				</tr>
			@endforeach
				<tr class="cart_total">
					<td colspan="2"><strong>@lang('custom.total')</strong></td>
					<td class="text-right"><strong>CHF {{Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'])}}</strong></td>
				</tr>
			</tbody>
		</table>
	</div>
	
	{{-- Clear Cart --}}
	<div><a href="javascript: void(0);" class="clear_cart"><strong>@lang('custom.clear_order')</strong></a></div>
	
	{{-- Checkout --}}
	<button type="button" class="btn btn-width mt30 checkout_cart" id="checkout_cart">@lang('custom.home_cart_checkout_btn') CHF {{Helper::formatToTwoDecimalPlaces($cartDetails['totalCartPrice'])}}</button>	

	@php
	if (in_array(App::getLocale(), Helper::WEBITE_LANGUAGES)) {
		$jsLang = App::getLocale();
	@endphp
	<script src="{{asset('js/site/development_site_cart_'.$jsLang.'.js')}}"></script>
	@php
	}
	@endphp

@else
	<div class="noOrder">
		<i class="siteicon icon_busket"></i>
		<h3 class="subheading">@lang('custom.message_empty_order')</h3>
		<div>@lang('custom.suggession_empty_cart')</div>
		<div class="mt5 min-order-value-emty-cart"><strong>@lang('custom.message_min_order') <span class="black">CHF {{Helper::formatToTwoDecimalPlaces(Cookie::get('minimum_order_amount'))}}</span></strong></div>
	</div>
@endif