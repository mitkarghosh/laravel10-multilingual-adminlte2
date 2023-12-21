<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// \URL::forceScheme('https');
include('admin.php');

Route::get('/clear-cache', function() {
      $exitCode = Artisan::call('config:cache');
      $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
   $exitCode = Artisan::call('view:clear');
   $exitCode = Artisan::call('cache:clear');
   // echo "Successfully cache clear!";
 
    return "Your all Cache is cleared";
});

Route::group(['namespace' => 'site', 'as' => 'site.'], function () {

    Route::get('/', function () {
        return redirect('/'.App::getLocale());
    });

    $locales = ['en','de'];
    foreach ($locales as $locale) {
        
        Route::group(['prefix' => $locale, 'as'=>$locale.'.'], function() {
            Route::get('/', 'HomeController@index')->name('home');

            /* User */
            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                Route::any('/login', 'UsersController@login')->name('login');
                Route::any('/register', 'UsersController@register')->name('register');
                Route::any('/forgot-password', 'UsersController@forgotPassword')->name('forgot-password');
                Route::any('/change-password', 'UsersController@changePassword')->name('change-password');
                Route::any('/reset-password/{token}', 'UsersController@resetPassword')->name('reset-password');
                
                Route::any('/guest-login', 'UsersController@guestLogin')->name('guest-login');

                /* Authenticated sections */
                Route::group(['middleware' => 'guest:web'], function () {
                    Route::any('/personal-details', 'UsersController@personalDetails')->name('personal-details');
                    Route::any('/change-avatar', 'UsersController@changeAvatar')->name('change-avatar');
                    Route::any('/delivery-address', 'UsersController@deliveryAddress')->name('delivery-address');
                    Route::any('/add-address', 'UsersController@addAddress')->name('add-address');
                    Route::any('/edit-address/{id}', 'UsersController@editAddress')->name('edit-address');
                    Route::any('/delete-address', 'UsersController@deleteAddress')->name('delete-address');
                    Route::any('/notifications', 'UsersController@notifications')->name('notifications');
                    Route::any('/orders-reviews', 'OrdersController@ordersReviews')->name('orders-reviews');
                    Route::any('/order-details/{order_id}', 'OrdersController@orderDetails')->name('order-details');
                    Route::any('/invoice-print/{order_id}', 'OrdersController@invoicePrint')->name('invoice-print');
                    Route::any('/order-review-submit', 'OrdersController@orderReviewSubmit')->name('order-review-submit');
                    Route::any('/change-user-password', 'UsersController@changeUserPassword')->name('change-user-password');
                    Route::any('/logout', 'UsersController@logout')->name('logout');
                });
            });

            // Cart section
            Route::any('/pin-code-availability', 'HomeController@pinCodeAvailability')->name('pin-code-availability');
            Route::any('/ingredients-with-product-price','CartController@ingredientsWithProductPrice')->name('ingredients-with-product-price');            
            Route::any('/add-to-cart', 'CartController@addToCart')->name('add-to-cart');
            Route::any('/get-cart-details', 'CartController@getCartDetails')->name('get-cart-details');
            Route::any('/clear-cart', 'CartController@clearCart')->name('clear-cart');
            Route::any('/update-cart-item', 'CartController@updateCartItem')->name('update-cart-item');
            Route::any('/remove-cart-item', 'CartController@removeCartItem')->name('remove-cart-item');

            Route::any('/apply-coupon', 'CartController@applyCoupon')->name('apply-coupon');
            Route::any('/remove-coupon', 'CartController@removeCoupon')->name('remove-coupon');
            Route::any('/calculate-card-amount', 'CartController@calculateCardAmount')->name('calculate-card-amount');
            Route::any('/regenerate-stripe-form', 'CartController@regenerateStripeForm')->name('regenerate-stripe-form');
            
            Route::any('/reviews', 'HomeController@reviews')->name('reviews');
            Route::any('/info', 'HomeController@info')->name('info');
            Route::any('/help', 'HomeController@help')->name('help');
            Route::any('/help-details/{id}', 'HomeController@helpDetails')->name('help-details');
            Route::any('/reservation', 'HomeController@reservation')->name('reservation');
            Route::any('/privacy-policy', 'HomeController@privacyPolicy')->name('privacy-policy');
            Route::any('/imprint', 'HomeController@colofon')->name('colofon');

            //payrex
            Route::any('/payByPayrex', 'CheckoutController@payByPayrex')->name('payByPayrex');
            Route::any('/cancelByPayrex', 'CheckoutController@cancelByPayrex')->name('cancelByPayrex');
            Route::any('/payrex-payment-sucess', 'CheckoutController@successByPayrex')->name('payrexsuccess');
			Route::any('/webhook-payrex-payment-sucess', 'CheckoutController@webhookSuccessByPayrex')->name('webhook-payrexsuccess');
            Route::any('/payrex-payment-redirect-thank-you-page', 'CheckoutController@payrexPaymentRedirectThankYouPage')->name('payrex-payment-redirect-thank-you-page');

            // Checkout
            Route::any('/get-delivery-slots', 'CheckoutController@dateWiseDeliverySlots')->name('get-delivery-slots');
            Route::any('/checkout-process', 'CheckoutController@index')->name('checkout-process');
            Route::any('/checkout', 'CheckoutController@checkout')->name('checkout');
            Route::post('/get-delivery-charge', 'CheckoutController@pinCodeWiseDeliveryCharge')->name('get-delivery-charge');
            Route::any('/guest-checkout', 'CheckoutController@guestCheckout')->name('guest-checkout');
            Route::any('/checkout-add-address', 'UsersController@checkoutAddAddress')->name('checkout-add-address');
            Route::any('/checking-restaurant-slot-availability', 'CheckoutController@checkingRestaurantSlotAvailability')->name('checking-restaurant-slot-availability');
            Route::any('/place-order', 'CheckoutController@placeOrder')->name('place-order');
            Route::any('/payment-process-stripe', 'CheckoutController@paymentProcessStripe')->name('payment-process-stripe');
            Route::any('/thank-you/{orderId}', 'CheckoutController@thankYou')->name('thank-you');
        });
    }
});
