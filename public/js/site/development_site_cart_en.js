$(document).ready(function() {
    var websiteLink = $('#website_link').val();
    var siteLang = $('#website_lang').val();
    var ajax_check = false;

    // clear cart
    $('.clear_cart').on('click', function() {
        swal.fire({
			// title: 'Clear Order',
			text: 'Are you sure to clear order?',
            icon: 'warning',
            allowOutsideClick: false,
            confirmButtonColor: '#1279cf',
            cancelButtonColor: '#333333',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
            // closeOnConfirm: false,
		}).then((result) => {
			if (result.value) {
                $('body').addClass('clicked xx');
                if (ajax_check) {
                    return;
                }
                ajax_check = true;
                var clearCartUrl = websiteLink + '/' + siteLang + '/clear-cart';
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: clearCartUrl,
                    method: 'POST',
                    data: {
                    },
                    success: function (cartResponse) {
                        ajax_check = false;
                        $('body').removeClass('clicked').addClass('yy');
                        // hide drinks section
                        $('#drinks_items').hide();
                        // fetching cart details
                        getCartDetails();
                    }
                });
            }
		});
    });

    // Update cart
    $('.updateCartItem').on('click', function () {
        var cart_status         = $(this).data('cart_status');
        var order_id            = $(this).data('order_id');
        var order_details_id    = $(this).data('order_details_id');
        
        $('body').addClass('clicked');
        if (ajax_check) {
            return;
        }
        ajax_check = true;
        var updateCartUrl = websiteLink + '/' + siteLang + '/update-cart-item';
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: updateCartUrl,
            method: 'POST',
            data: {
                cartStatus: cart_status,
                orderId: order_id,
                orderDetailsId: order_details_id,
            },
            success: function (cartUpdateResponse) {
                ajax_check = false;

                $('body').removeClass('clicked');   // loader hide

                // fetching cart details
                getCartDetails();
            }
        });
    });

    // Checkout section from homepage/cart page
    $('.checkout_cart').on('click', function() {
        // var login_status = $(this).val();
        var delivery_option = $('#delivery_option').val();

        if (ajax_check) {
            return;
        }
        ajax_check = true;
        
        // Checkout section START
        var checkoutProcessUrl = websiteLink + '/' + siteLang + '/checkout-process';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: checkoutProcessUrl,
            method: 'POST',
            data: {
                // login_status: login_status,
                delivery_option: delivery_option,
            },
            success: function (checkoutResponse) {
                var response = jQuery.parseJSON(checkoutResponse);
                ajax_check = false;
                if (response.type == 'error') {
                   
                    swal.fire({
                        // title: response.title,
                        text: response.message,
                        icon: response.type,
                        allowOutsideClick: false,
                        confirmButtonColor: '#1279cf',
                        cancelButtonColor: '#333333',
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    });
                } else {
                    // redirect section
                    var redirectUrl = websiteLink + '/' + siteLang;
                    
                    if (response.redirectTo == 'checkout') {
                        window.location.href = redirectUrl + '/checkout';
                    }
                    else {
                        window.location.href = redirectUrl + '/guest-checkout';
                    }
                }
            }
        });

    });

    // Delivery option select
    $('.delivery_option_switcher').on('click', function() {
        var deliveryOption = $(this).data('deliveryoption');
        $('#delivery_option').val(deliveryOption);
    });
    
});

