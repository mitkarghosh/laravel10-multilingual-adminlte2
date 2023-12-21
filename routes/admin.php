<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['namespace' => 'admin', 'prefix' => 'securepanel', 'as' => 'admin.'], function () {

    Route::get('/', function () {
        return redirect('/securepanel/'.App::getLocale());
    });
    
    $locales = ['en','de'];
    foreach ($locales as $locale) {
        
        Route::group(['prefix' => $locale, 'as'=>$locale.'.'], function() {

            Route::any('/', 'AuthController@login')->name('login');
            Route::any('/forget-password', 'AuthController@forgetPassword')->name('forget-password');
            Route::any('/reset-password/{token}', 'AuthController@resetPassword')->name('reset-password');

            Route::group(['middleware' => 'backend'], function () {
                Route::any('/dashboard', 'AccountController@dashboard')->name('dashboard');
                Route::any('/edit-profile', 'AccountController@editProfile')->name('edit-profile');
                Route::any('/change-password', 'AccountController@changePassword')->name('change-password');
                Route::any('/logout', 'AuthController@logout')->name('logout');

                Route::group(['middleware' => 'admin'], function () {

                    Route::any('/payment-settings', 'AccountController@paymentSettings')->name('payment-settings');
                    Route::any('/change-payment-status/{id}/{type}', 'AccountController@changePaymentStatus')->name('change-payment-status');
                    Route::any('/delete-payment-gateway/{id}/{type}', 'AccountController@deletePaymentStatus')->name('delete-payment-gateway');
                    
                    
                    Route::any('/site-settings', 'AccountController@siteSettings')->name('site-settings');
                    Route::any('/update-shop-status', 'AccountController@updateShopStatus')->name('update-shop-status');
                    Route::any('/delivery-slots', 'AccountController@deliverySlots')->name('delivery-slots');
                    Route::any('/delivery-slot-delete/{id}', 'AccountController@deliverySlotDelete')->name('delivery-slot-delete');
            
                    Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
                        Route::get('/list', 'UsersController@list')->name('list');
                        Route::any('/show-all', 'UsersController@showAll')->name('show-all');
                        // Route::get('/add', 'UsersController@add')->name('add');
                        // Route::post('/add-submit', 'UsersController@add')->name('addsubmit');
                        // Route::get('/edit/{id}', 'UsersController@edit')->name('edit')->where('id','[0-9]+');
                        // Route::post('/edit-submit/{id}', 'UsersController@edit')->name('editsubmit');
                        Route::get('/status/{id}', 'UsersController@status')->name('change-status')->where('id','[0-9]+');
                        Route::any('/change-password/{id}', 'UsersController@changePassword')->name('change-password')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'UsersController@delete')->name('delete')->where('id','[0-9]+');
                    });
                    
                    Route::group(['prefix' => 'cms', 'as' => 'CMS.'], function () {
                        Route::get('/', 'CmsController@list')->name('list');
                        Route::get('/edit/{id}', 'CmsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::post('/edit-submit/{id}', 'CmsController@edit')->name('editsubmit')->where('id','[0-9]+');
                    });       
                    
                    Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
                        Route::any('/', 'RoleController@list')->name('list');
                        Route::post('/add-edit-action', 'RoleController@addEdit')->name('add-edit');
                        Route::any('/permission/{roleType}', 'RoleController@permission')->name('permission');
                        Route::post('/submit/{roleType}', 'RoleController@submitRolePermission')->name('submitpermission');
                        Route::any('/delete/{id}', 'RoleController@delete')->name('delete')->where('id','[0-9]+');
                    });
            
                    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
                        Route::get('/', 'CategoriesController@list')->name('list');
                        Route::any('/show-all', 'CategoriesController@showAll')->name('show-all');
                        Route::get('/add', 'CategoriesController@add')->name('add');
                        Route::post('/add-submit', 'CategoriesController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'CategoriesController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'CategoriesController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'CategoriesController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/sort-category', 'CategoriesController@sortCategory')->name('sort-category');
                        Route::post('/save-sort-category', 'CategoriesController@saveSortCategory')->name('save-sort-category');
                        Route::get('/delete/{id}', 'CategoriesController@delete')->name('delete')->where('id','[0-9]+');
                        Route::get('/sort-product/{id}', 'CategoriesController@sortProduct')->name('sort-product');
                        Route::post('/save-sort-product', 'CategoriesController@saveSortProduct')->name('save-sort-product');
                    });

                    Route::group(['prefix' => 'drink', 'as' => 'drink.'], function () {
                        Route::get('/', 'DrinksController@list')->name('list');
                        Route::any('/show-all', 'DrinksController@showAll')->name('show-all');
                        Route::get('/add', 'DrinksController@add')->name('add');
                        Route::post('/add-submit', 'DrinksController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'DrinksController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'DrinksController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'DrinksController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'DrinksController@delete')->name('delete')->where('id','[0-9]+');
                    });
                    
                    Route::group(['prefix' => 'ingredient', 'as' => 'ingredient.'], function () {
                        Route::get('/', 'IngredientsController@list')->name('list');
                        Route::any('/show-all', 'IngredientsController@showAll')->name('show-all');
                        Route::get('/add', 'IngredientsController@add')->name('add');
                        Route::post('/add-submit', 'IngredientsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'IngredientsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'IngredientsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'IngredientsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'IngredientsController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
                        Route::get('/', 'TagsController@list')->name('list');
                        Route::any('/show-all', 'TagsController@showAll')->name('show-all');
                        Route::get('/add', 'TagsController@add')->name('add');
                        Route::post('/add-submit', 'TagsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'TagsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'TagsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'TagsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'TagsController@delete')->name('delete')->where('id','[0-9]+');
                    });
                    
                    Route::group(['prefix' => 'allergen', 'as' => 'allergen.'], function () {
                        Route::get('/', 'AllergensController@list')->name('list');
                        Route::any('/show-all', 'AllergensController@showAll')->name('show-all');
                        Route::get('/add', 'AllergensController@add')->name('add');
                        Route::post('/add-submit', 'AllergensController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'AllergensController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'AllergensController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'AllergensController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'AllergensController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
                        Route::get('/', 'ProductsController@list')->name('list');
                        Route::any('/show-all', 'ProductsController@showAll')->name('show-all');



                        Route::get('/add/{id?}', 'ProductsController@add')->name('add');
                        Route::get('/copy/{id?}', 'ProductsController@copy')->name('copy');

                        Route::get('/addon-change-status/{id}', 'ProductsAddonController@ChangeStatus')->name('addon-change-status');
                        Route::get('/addonlist', 'ProductsAddonController@addonList')->name('addonlist');
                        Route::get('/add-addon', 'ProductsAddonController@addAddon')->name('add-addon');
                        Route::get('/edit-addon/{id?}', 'ProductsAddonController@editAddon')->name('edit-addon');
                        Route::post('/addon-submit', 'ProductsAddonController@addonSubmit')->name('addon-submit'); 
                        Route::get('/delete-addon/{id}', 'ProductsAddonController@deleteAddon')->name('delete-addon')->where('id','[0-9]+');
                        Route::get('/delete-addon-ajax/{id}', 'ProductsAddonController@deleteAddonAjax')->name('delete-addon-ajax')->where('id','[0-9]+');
                        


                       // Route::get('/add', 'ProductsController@add')->name('add');
                        Route::post('/add-submit', 'ProductsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'ProductsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'ProductsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'ProductsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'ProductsController@delete')->name('delete')->where('id','[0-9]+');
                        Route::any('/change-status-product-attribute', 'ProductsController@changeStatusProductAttribute')->name('change-status-product-attribute');
                        Route::any('/delete-product-attribute', 'ProductsController@deleteProductAttribute')->name('delete-product-attribute');
                        Route::any('/delete-dropdown-title', 'ProductsController@deleteProductDropdownTitle')->name('delete-dropdown-title');
                        Route::any('/delete-dropdown-values', 'ProductsController@deleteProductDropdownValues')->name('delete-dropdown-values');
                    });

                    Route::group(['prefix' => 'specialMenu', 'as' => 'specialMenu.'], function () {
                        Route::get('/', 'SpecialMenusController@list')->name('list');
                        Route::any('/show-all', 'SpecialMenusController@showAll')->name('show-all');
                        Route::get('/add', 'SpecialMenusController@add')->name('add');
                        Route::post('/add-submit', 'SpecialMenusController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'SpecialMenusController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'SpecialMenusController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'SpecialMenusController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'SpecialMenusController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'avatar', 'as' => 'avatar.'], function () {
                        Route::get('/', 'AvatarsController@list')->name('list');
                        Route::any('/show-all', 'AvatarsController@showAll')->name('show-all');
                        Route::get('/add', 'AvatarsController@add')->name('add');
                        Route::post('/add-submit', 'AvatarsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'AvatarsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'AvatarsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'AvatarsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'AvatarsController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'pinCode', 'as' => 'pinCode.'], function () {
                        Route::get('/', 'PinCodesController@list')->name('list');
                        Route::any('/show-all', 'PinCodesController@showAll')->name('show-all');
                        Route::get('/add', 'PinCodesController@add')->name('add');
                        Route::post('/add-submit', 'PinCodesController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'PinCodesController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'PinCodesController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'PinCodesController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'PinCodesController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    // Route::group(['prefix' => 'deliveryArea', 'as' => 'deliveryArea.'], function () {
                    //     Route::get('/', 'DeliveryAreasController@list')->name('list');
                    //     Route::get('/add', 'DeliveryAreasController@add')->name('add');
                    //     Route::post('/add-submit', 'DeliveryAreasController@add')->name('addsubmit');            
                    //     Route::get('/edit/{id}', 'DeliveryAreasController@edit')->name('edit')->where('id','[0-9]+');
                    //     Route::any('/edit-submit/{id}', 'DeliveryAreasController@edit')->name('editsubmit')->where('id','[0-9]+');
                    //     Route::get('/status/{id}', 'DeliveryAreasController@status')->name('change-status')->where('id','[0-9]+');
                    //     Route::get('/delete/{id}', 'DeliveryAreasController@delete')->name('delete')->where('id','[0-9]+');
                    // });

                    Route::group(['prefix' => 'faq', 'as' => 'faq.'], function () {
                        Route::get('/', 'FaqsController@list')->name('list');
                        Route::any('/show-all', 'FaqsController@showAll')->name('show-all');
                        Route::get('/add', 'FaqsController@add')->name('add');
                        Route::post('/add-submit', 'FaqsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'FaqsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'FaqsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'FaqsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'FaqsController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'help', 'as' => 'help.'], function () {
                        Route::get('/', 'HelpsController@list')->name('list');
                        Route::any('/show-all', 'HelpsController@showAll')->name('show-all');
                        Route::get('/add', 'HelpsController@add')->name('add');
                        Route::post('/add-submit', 'HelpsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'HelpsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'HelpsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'HelpsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'HelpsController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'orders', 'as' => 'order.'], function () {
                        Route::get('/', 'OrdersController@list')->name('list');
                        Route::any('/show-all', 'OrdersController@showAll')->name('show-all');
                        Route::get('/details/{id}', 'OrdersController@details')->name('details')->where('id','[0-9]+');
                        Route::get('/invoice/{id}', 'OrdersController@invoice')->name('invoice')->where('id','[0-9]+');
                        Route::get('/invoice-print/{id}', 'OrdersController@invoicePrint')->name('invoice-print')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'OrdersController@status')->name('change-status')->where('id','[0-9]+');
                        Route::any('/processing-status', 'OrdersController@processingStatus')->name('processing-status');
                        Route::any('/cancel-order', 'OrdersController@cancelOrder')->name('cancel-order');
                        // Route::any('/live-orders', 'OrdersController@liveOrders')->name('live-orders');
                        // Route::get('/live-order-list', 'OrdersController@liveOrderList')->name('live-order-list');
                        Route::post('/delivery-in', 'OrdersController@deliveryIn')->name('delivery-in');

                        Route::get('/export-to-excel', 'OrdersController@exportToExcel')->name('export-to-excel');
                        Route::get('/export-to-pdf', 'OrdersController@exportToPdf')->name('export-to-pdf');
                    });

                    Route::group(['prefix' => 'liveOrders', 'as' => 'liveOrder.'], function () {
                        Route::any('/live-orders', 'OrdersController@liveOrders')->name('live-orders');
                        Route::get('/live-order-list', 'OrdersController@liveOrderList')->name('live-order-list');
                    });

                    Route::group(['prefix' => 'reviews', 'as' => 'review.'], function () {
                        Route::get('/', 'ReviewsController@list')->name('list');
                        Route::any('/show-all', 'ReviewsController@showAll')->name('show-all');
                        Route::get('/details/{id}', 'ReviewsController@details')->name('details')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'ReviewsController@delete')->name('delete')->where('id','[0-9]+');
                    });
                    
                    /*===============SubAdmin management=====================*/
                    Route::group(['prefix' => 'subadmin', 'as' => 'subAdmin.'], function () {
                        Route::get('/', 'SubAdminController@list')->name('list');
                        Route::any('/show-all', 'SubAdminController@showAll')->name('show-all');
                        Route::get('/add', 'SubAdminController@add')->name('add');
                        Route::post('/add-submit', 'SubAdminController@add')->name('addsubmit');
                        Route::get('/edit/{id}', 'SubAdminController@edit')->name('edit')->where('id','[0-9]+');
                        Route::any('/edit-submit/{id}', 'SubAdminController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'SubAdminController@status')->name('change-status')->where('id','[0-9]+');                        
                        Route::get('/delete/{id}', 'SubAdminController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    /*===============Role management=====================*/
                    Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
                        Route::get('/', 'RoleController@list')->name('list');
                        Route::any('/show-all', 'RoleController@showAll')->name('show-all');
                        Route::get('/add', 'RoleController@add')->name('add');
                        Route::post('/add-submit', 'RoleController@add')->name('addsubmit');
                        Route::get('/edit/{id}', 'RoleController@edit')->name('edit');
                        Route::any('/edit-submit/{id}', 'RoleController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'RoleController@delete')->name('delete')->where('id','[0-9]+');
                    });

                    Route::group(['prefix' => 'coupons', 'as' => 'coupon.'], function () {
                        Route::get('/', 'CouponsController@list')->name('list');
                        Route::any('/show-all', 'CouponsController@showAll')->name('show-all');
                        Route::get('/add', 'CouponsController@add')->name('add');
                        Route::post('/add-submit', 'CouponsController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'CouponsController@edit')->name('edit')->where('id','[0-9]+');
                        Route::post('/edit-submit/{id}', 'CouponsController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'CouponsController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'CouponsController@delete')->name('delete')->where('id','[0-9]+');
                    });
                    
                    Route::group(['prefix' => 'specialHours', 'as' => 'specialHour.'], function () {
                        Route::get('/', 'SpecialHoursController@list')->name('list');
                        Route::any('/show-all', 'SpecialHoursController@showAll')->name('show-all');
                        Route::get('/add', 'SpecialHoursController@add')->name('add');
                        Route::post('/add-submit', 'SpecialHoursController@add')->name('addsubmit');            
                        Route::get('/edit/{id}', 'SpecialHoursController@edit')->name('edit')->where('id','[0-9]+');
                        Route::post('/edit-submit/{id}', 'SpecialHoursController@edit')->name('editsubmit')->where('id','[0-9]+');
                        Route::get('/status/{id}', 'SpecialHoursController@status')->name('change-status')->where('id','[0-9]+');
                        Route::get('/delete/{id}', 'SpecialHoursController@delete')->name('delete')->where('id','[0-9]+');
                        Route::any('/slot-delete/{id}', 'SpecialHoursController@slotDelete')->name('slot-delete')->where('id','[0-9]+');
                    });
                    
                });

            });

        });
    }

});