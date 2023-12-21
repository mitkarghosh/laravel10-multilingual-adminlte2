$.validator.addMethod("valid_email", function(value, element) {
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid email");

//Phone number eg. (+91)9876543210
$.validator.addMethod("valid_number", function(value, element) {    
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        return true;
    } else {
        return false;
    }

}, "Please enter a valid phone number");

//Phone number eg. +919876543210
$.validator.addMethod("valid_site_number", function(value, element) {
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        
        if($("#phone_no").val().charAt(0) == '0') {
            return false;
        }
        if($("#phone_no").val().substring(0, 3) == '966') {
            return false;
        }
        return true;
    } else {
        return false;
    }
}, "Please enter a valid phone number");

//minimum 8 digit,small+capital letter,number,specialcharacter
$.validator.addMethod("valid_password", function(value, element) {
    if (/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/.test(value)) {
        return true;
    } else {
        return false;
    }
});

//Alphabet or number
$.validator.addMethod("valid_coupon_code", function(value, element) {
    if (/^[a-zA-Z0-9]+$/.test(value)) {
        return true;
    } else {
        return false;
    }
});

//Integer and decimal
$.validator.addMethod("valid_amount", function(value, element) {
    if (/^[1-9]\d*(\.\d+)?$/.test(value)) {
        return true;
    } else {
        return false;
    }
});

//Pack value 
$.validator.addMethod("pack_value", function(value, element) {
    //if (/^(?=.*[0-9])[- +()0-9]+$/.test(value)) {
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, 'Please enter valid pack value');

//Pack value create bid 
$.validator.addMethod("pack_value_create_bid", function(value, element) {
    
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        return true;
    } else {
        $("#error_bids").html('');
        return false;
    }
}, 'Please enter valid pack value');

// quantity value check for create bid
$.validator.addMethod("quantity_create_bid", function(value, element) {
    
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        return true;
    } else {
        $("#error_bids").html('');
        return false;
    }
}, 'Please enter valid quantity');

// check value of minimum amount for create bid
$.validator.addMethod("minimum_amount_create_bid", function(value, element) {
    
    if (/^(?:[+]9)?[0-9]+$/.test(value)) {
        return true;
    } else {
        $("#error_bids").html('');
        return false;
    }
}, 'Please enter valid minimum amount');


//mrp
$.validator.addMethod("mrp", function(value, element) {
   
    if (/^[1-9]\d*(\.\d+)?$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, 'Please enter valid amount');

//selling_price
$.validator.addMethod("selling_price", function(value, element) {
    //if (/^(?=.*[0-9])[- +()0-9]+$/.test(value)) {
    if (/^[1-9]\d*(\.\d+)?$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, 'Please enter valid amount');

//End date should be greater than Start date
$.validator.addMethod("greaterThan", function(value, element, params) {
    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date($(params).val());
    }
    return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
}, 'Must be greater than start date');

//End date should be greater than Start date for create bid
$.validator.addMethod("greaterThanED_create_bid", function(value, element, params) {
    $("#error_bids").html('');
    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date($(params).val());
    }
    return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
}, 'Must be greater than start date');


$(document).ready(function() {
    var websiteLink = $('#website_link').val();
    var siteLang = $('#website_lang').val();
    var ajax_check = false;

    $('#website_language').on('change', function() {
        var langValue = $(this).val();
        
        //var setLangUrl = websiteLink + '/' + langValue;
        // Code Edit ----------------------
        var websiteUrl = window.location.href;
        var splitAll = websiteUrl.split('/');
        var keyVal = '';
       
        splitAll.forEach(function(item, index){
            if(item == 'en' || item == 'de') {
                keyVal = index;
            }
        });
        splitAll[keyVal]= langValue;
        
        var setLangUrl  =  splitAll.join('/');
        // Code Edit ----------------------
        
        window.location = setLangUrl;        
    });

    $(document).on('click', '.dashboard_website_language', function() {
        var langValue = $(this).data('lang');
        setCheckoutFormValue();
        //var setLangUrl = websiteLink + '/' + langValue;
        // Code Edit ----------------------
        var websiteUrl = window.location.href;
        var splitAll = websiteUrl.split('/');
        var keyVal = '';
       
        splitAll.forEach(function(item, index){
            if(item == 'en' || item == 'de') {
                keyVal = index;
            }
        });
        splitAll[keyVal]= langValue;
        
        var setLangUrl  =  splitAll.join('/');
        // Code Edit ----------------------
        
        window.location = setLangUrl;        
    });

    /* User Registration Form */
    $("#registrationForm").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            last_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            email: {
                required: true,
                valid_email: true
            },
            password: {
                required: true,
                // valid_password: true,
            },
            confirm_password: {
                required: true,
                // valid_password: true,
                equalTo: "#password"
            },
            agree: {
                required: true
            },            
        },
        messages: {
           first_name: {
                required: "Please enter first name",
                minlength: "First Name should be at least 2 characters",
                maxlength: "First Name must not be more than 255 characters"
            },
            last_name: {
                required: "Please enter last name",
                minlength: "Last Name should be at least 2 characters",
                maxlength: "Last Name must not be more than 255 characters"
            },
            password: {
                required: "Please enter password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            confirm_password: {
                required: "Please enter confirm password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter",
                equalTo: "Password should be same as create password",
            },
            agree: {
                required: "Please select terms and conditions",
            },
        },
        errorPlacement: function(error, element) {
            // error.insertAfter(element);
            error.appendTo($(element).parents('.labelWrap').next());
        },
        submitHandler: function(form) {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {     // recaptcha failed validation
                $('#recaptcha-error').html('Please verify that you are human');
                $('#recaptcha-error').show();
              return false;
            } else {    //recaptcha passed validation
                $('#recaptcha-error').html('');
                $('#recaptcha-error').hide();
                form.submit();
            }
        }
    });

    /* User Login Form */
    $("#loginForm").validate({
        rules: {
            email: {
                required: true,
                valid_email: true
            },
            password: {
                required: true,
            }
        },
        messages: {
            credential: {
                required: "Please enter email"
            },
            password: {
                required: "Please enter password",
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* change password Form */
    $("#forgetPasswordForm").validate({
        rules: {
            email: {
                required: true,
                valid_email: true
            }
        },
        messages: {
            email: {
                required: "Please enter email"
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* reset password Form */
    $("#resetPasswordForm").validate({
        rules: {
            password: {
                required: true,
                // valid_password: true,
            },
            confirm_password: {
                required: true,
                // valid_password: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please enter password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            confirm_password: {
                required: "Please enter confirm password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter",
                equalTo: "Password should be same as password",
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Personal Details Form */
    $("#personalDetails").validate({
        rules: {
            // nickname: {
            //     required: true,
            // },
            // title: {
            //     required: true,
            // },
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            last_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            login_language: {
                required: true,
            },
            email: {
                required: true,
                valid_email: true
            },
            phone_no: {
                required: true,
            },
            dob: {
                required: true
            },
            
        },
        messages: {
            nickname: {
                required: "Please enter nickname",
            },
            title: {
                required: "Please select title",
            },
            first_name: {
                required: "Please enter first name",
                minlength: "First Name should be at least 2 characters",
                maxlength: "First Name must not be more than 255 characters"
            },
            last_name: {
                required: "Please enter last name",
                minlength: "Last Name should be at least 2 characters",
                maxlength: "Last Name must not be more than 255 characters"
            },
            login_language: {
                required: "Please select login language",
            },
            email: {
                required: "Please enter email",
                valid_email: "Please enter valid email",
            },
            phone_no: {
                required: "Please enter contact number",
                valid_only_number: "Please enter contact number without country code"
            },
            dob: {
                required: "Please select date of birth",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            var phoneNo=$('#phone_no').val();
            if(phoneNo.includes("+")){
                form.submit();
            }
        }
    });

    /***
     * Check Phone Number 
     */
     $(document).on('keyup','#phone_no',function(){
        $(document).find('.c_code_error').remove();
        if($(this).val()){
            if($('.profile-update-form').length){
                var phoneNo=$('#phone_no').val();
                if(phoneNo.includes("+")==false){ 
                    $(this).after("<div class='c_code_error'>Please enter contact number with country code.</div>")
                }
            }
        }
     })


    /* change user password Form */
    $("#changeUserPasswordForm").validate({
        rules: {
            current_password: {
                required: true,
                // valid_password: true,
            },
            password: {
                required: true,
                // valid_password: true,
            },
            confirm_password: {
                required: true,
                // valid_password: true,
                equalTo: "#password"
            }
        },
        messages: {
            current_password: {
                required: "Please enter password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            password: {
                required: "Please enter new password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            confirm_password: {
                required: "Please enter confirm password",
                // valid_password: "Min. 8, alphanumeric, special character & a capital letter",
                equalTo: "Password should be same as new password",
            }
        }, errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    
    /* Add address Form */
    $("#addAddressForm").validate({
        rules: {
            // company: {
            //     required: true,
            // },
            street: {
                required: true,
            },
            // floor: {
            //     required: true,
            // },
            // door_code: {
            //     required: true,
            // },
            post_code: {
                required: true,
            },
            city: {
                required: true,
            },
            addressAlias: {
                required: true,
            },
            city: {
                required: true,
            },
            customAlias: {
                required: true,
            },            
        },
        messages: {
            // company: {
            //     required: "Please enter company or c/o",
            // },
            street: {
                required: "Please enter street and number",
            },
            // floor: {
            //     required: "Please enter floor",
            // },
            // door_code: {
            //     required: "Please enter door code",
            // },
            post_code: {
                required: "Please enter post code",
            },
            city: {
                required: "Please enter city",
            },
            addressAlias: {
                required: "Please select address type",
            },
            customAlias: {
                required: "Please enter your own alias",
            },            
        }, errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Edit address Form */
    $("#editAddressForm").validate({
        rules: {
            // company: {
            //     required: true,
            // },
            street: {
                required: true,
            },
            // floor: {
            //     required: true,
            // },
            // door_code: {
            //     required: true,
            // },
            post_code: {
                required: true,
            },
            city: {
                required: true,
            },
            addressAlias: {
                required: true,
            },
            city: {
                required: true,
            },
            customAlias: {
                required: true,
            },            
        },
        messages: {
            // company: {
            //     required: "Please enter company or c/o",
            // },
            street: {
                required: "Please enter street and number",
            },
            // floor: {
            //     required: "Please enter floor",
            // },
            // door_code: {
            //     required: "Please enter door code",
            // },
            post_code: {
                required: "Please enter post code",
            },
            city: {
                required: "Please enter city",
            },
            addressAlias: {
                required: "Please select address type",
            },
            customAlias: {
                required: "Please enter your own alias",
            },            
        }, errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Add address Form */
    $("#orderReviewForm").validate({
        ignore: [],
        rules: {
            food_quality: {
                required: true,
            },
            delivery_time: {
                required: true,
            },
            driver_friendliness: {
                required: true,
            },
            // short_review: {
            //     required: true,
            // },
        },
        messages: {
            food_quality: {
                required: "Please rate food quality",
            },
            delivery_time: {
                required: "Please rate delivery time",
            },
            driver_friendliness: {
                required: "Please rate driver friendliness",
            },
            // short_review: {
            //     required: "Please leave a short review",
            // },
        }, errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    
    /************************************* Cart Start **********************************/
    // Without Product Attributes - Ingredients check and uncheck
    $(".ingredients").on('click', function () {
        var product_id      = $(this).data('proid');       // without encryption
        var productId       = $(this).data('productid');
        var ingredientId    = $(this).data('ingredientid');
        // var ingredientId    = $(this).val();

        var existingIngredientIds = '';
        existingIngredientIds = $('#product_without_attribute_ingredient_ids_'+product_id).val();
        if (existingIngredientIds != '') {
            noProductAttributeIngredientIds = existingIngredientIds.split(',');
        } else {
            noProductAttributeIngredientIds = [];
        }
        var stringExistingCheck = existingIngredientIds.search(ingredientId);
        if (stringExistingCheck == -1) {
            noProductAttributeIngredientIds.push(ingredientId);
            $('#product_without_attribute_ingredient_ids_'+product_id).val(noProductAttributeIngredientIds);
        } else {
            var strArray = '';
            strArray = existingIngredientIds.split(',');
            for (var i = 0; i < strArray.length; i++) {
                if (strArray[i] == ingredientId) {
                    strArray.splice(i, 1);
                    noProductAttributeIngredientIds.splice(i, 1);
                }
            }
            $('#product_without_attribute_ingredient_ids_'+product_id).val('');
            $('#product_without_attribute_ingredient_ids_'+product_id).val(strArray);
        }
        
        if (productId != '') {
            var selectedIngredients = $('#product_without_attribute_ingredient_ids_'+product_id).val();
            // $('#whole-area').show(); //Showing loader
            // $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            ajax_check = true;
            var ingredientsWithProductPriceUrl = websiteLink + '/' + siteLang + '/ingredients-with-product-price';
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: ingredientsWithProductPriceUrl,
                method: 'POST',
                data: {
                    productId: productId,
                    selectedIngredients: selectedIngredients,
                },
                success: function (ingredientResponse) {
                    ajax_check = false;
                    // console.log(ingredientResponse);
                    $('#product_without_attribute_price_'+product_id).val(ingredientResponse);
                    $('#product_without_attribute_ingredient_price_'+product_id).html(ingredientResponse);
                }
            });
        }
    });

    // Single & Multiple drop down on change start
    $('.tt_qtyInput').on('keyup', function() {
        var prodId = $(this).data('prodid');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        if (quantity == '' || quantity == 0) {
            quantity = 1;
        }
        updatedTotalPrice(prodId, quantity);
    });
    $('.tt_qtyAdd').on('click', function() {
        var prodId = $(this).data('prodid');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        quantity = (parseInt(quantity) + 1);
        updatedTotalPrice(prodId, quantity);
    });
    $('.tt_qtyMinus').on('click', function() {
        var prodId = $(this).data('prodid');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        quantity = (parseInt(quantity) - 1);
        if (quantity < 1) {
            quantity = 1;
        }
        updatedTotalPrice(prodId, quantity);
    });
    $('.singleDropDown').on('change', function() {
        var prodId = $(this).data('prodid');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        updatedTotalPrice(prodId, quantity);
    });
    $('.multipleDropDown').on('change', function () {
        var prodId = $(this).data('prodid');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        updatedTotalPrice(prodId, quantity);
    });
    function updatedTotalPrice(prodId, quantity) {
        var updatedProPrice = mainProPrice = singleDropDownTotalSelectedPrice = multipleDropDownTotalSelectedPrice = 0;

        if ($('#anchor_prod_id_'+prodId).data('proprice')) {
            var mainProPrice = $('#anchor_prod_id_'+prodId).data('proprice');    
        }
                
        // single drop down price collect Start
        $('.dropdown_menu_value_'+prodId).each(function() {
            var dropDownPrice = $(this).find(':selected').data('ddprice');
            singleDropDownTotalSelectedPrice += parseFloat(dropDownPrice);
        });
        // single drop down price collect End
        // multiple drop down price collect Start
        $('.dropdown_menu_value_multiple_'+prodId).each(function() {
            if ($(this).is(":checked")) {
                multipleDropDownTotalSelectedPrice += $(this).data('ddprice');
            }
        });
        // multiple drop down price collect End

        updatedProPrice = (parseFloat(mainProPrice) + parseFloat(singleDropDownTotalSelectedPrice) + parseFloat(multipleDropDownTotalSelectedPrice)) * quantity;

        if ($("#anchor_prod_id_"+prodId).data('hasattribute') == 'Y') {
            $('.havingAttribute_'+prodId).each(function() {
                var updatedProAttributePrice = parseFloat($(this).data('bp')) * quantity;
                $(this).text(updatedProAttributePrice.toFixed(2));
            });
        } else {
            $('#updated_prod_id_'+prodId).text(updatedProPrice.toFixed(2));
        }
    }
    // Single & Multiple drop down on change end

    // Delivery option select
    $('.delivery_option_switcher').on('click', function() {
        var deliveryOption = $(this).data('deliveryoption');
        $('#delivery_option').val(deliveryOption);
        clickCollectCheck(deliveryOption);
    });

    /**
     * Clear hold card data
     */
    $(document).on('click','.tt_modal_close',function(){

        localStorage.removeItem("holdCardData");
        //addToCardHoldData();

    })

    /**
     * Hold data add to card
     */
     function addToCardHoldData(type=''){

        var carddata=localStorage.getItem('holdCardData');
        
        
        if ((productId != '' || specialId != '' || drinkId != '') && carddata) {
            localStorage.removeItem("holdCardData");
            var carddatas=JSON.parse(carddata);
            var productId=carddatas.productId;
            var showIngredients=carddatas.showIngredients;
            var ingredientIds=carddatas.ingredientIds;
            var hasAttribute=carddatas.hasAttribute;
            var attributeId=carddatas.attributeId;
            var specialId=carddatas.specialId;
            var  drinkId=carddatas.drinkId;
            var isMenu=carddatas.isMenu;
            var menuValueIds=carddatas.menuValueIds;
            var  quantity=carddatas.quantity; 
            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            ajax_check = true;

            // Add to cart section START
            var addToCartUrl = websiteLink + '/' + siteLang + '/add-to-cart';
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: addToCartUrl,
                method: 'POST',
                cache: false,
                data: {
                    productId: productId,
                    showIngredients: showIngredients,
                    ingredientIds: ingredientIds,
                    hasAttribute: hasAttribute,
                    attributeId: attributeId,
                    specialId: specialId,
                    drinkId: drinkId,
                    isMenu: isMenu,
                    menuValueIds: menuValueIds,
                    quantity: quantity
                },
                success: function (cartResponse) {
                    ajax_check = false;
                    var data = jQuery.parseJSON(cartResponse);
                    if(type=='reloadpage'){
                        localStorage.setItem('cartactionpincode',1);
                        location.reload();
                     }
                    if (showIngredients != '') {
                        $('#product_without_attribute_ingredient_ids_'+prodId).val('');
                        $('#product_without_attribute_price_'+prodId).val(0);
                        $('.ingredients_checkbox_'+prodId).prop("checked", false);
                        $('#product_without_attribute_ingredient_price_'+prodId).html($('#product_previous_price_'+prodId).val());
                    }
                    // if attribute exist
                    if (hasAttribute == 'Y') {
                        $('.havingAttribute_'+prodId).each(function() {
                            $(this).text($(this).data('bp'));
                        });
                    }

                    // if (menuValueIds != '') {
                    //     $('.dropdown_menu_value_'+prodId).val('');
                    // }
                    
                    $('body').removeClass('clicked');   // loader hide
                    if (data.type == 'success') {
                        $('#tt_qtyInput_'+prodId).val(1);
                        
                        var getCartDataUrl = websiteLink + '/' + siteLang + '/get-cart-details';
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: getCartDataUrl,
                            method: 'GET',
                            data: {
                            },
                            success: function (cartDetailsResponse) {
                                // $('body').removeClass('clicked');

                                // Cart add or update message display
                                $('#cart_success_message').html('');
                                $('#cart_success_message').removeClass('alert-warning');
                                $('#cart_success_message').addClass('alert-success');
                                $('#cart_success_message').html('<strong>'+data.message+'</strong>');
                                $("#cart_success_message").slideDown(500).delay(1000);

                                $('#cartDetails').html(cartDetailsResponse.html);

                                // if (cartDetailsResponse.totalCartPrice > 0) {
                                if (cartDetailsResponse.productExist != 0) {
                                    if(!$('.mainBody').hasClass('withCartValue'))
                                        $('.mainBody').addClass('withCartValue');
                                    $('#mobile_cart_checkout').show();
                                    $('#mobile_cart_amount').html(cartDetailsResponse.totalCartPrice);
                                }

                                // Drinks items
                                if (prodId != '') {
                                    $('#drinks_items').show(300);
                                }

                                // Minimum order section
                                if (cartDetailsResponse.minOrderMessageStatus == 1) {
                                    $('#remaining_amount').html(cartDetailsResponse.remainingToAvoidMinimumOrder);
                                    $('#min_cart_div').show(500);
                                } else {
                                    $('#min_cart_div').hide();
                                    $('#remaining_amount').html('');
                                }

                                setTimeout(function() {
                                    $("#cart_success_message").slideUp(1000).delay(1000);
                                }, 100);

                                // reset to default
                                $('.singleDropDown').prop('selectedIndex',0);
                                $('.multipleDropDown').prop('checked', false);
                                $('#updated_prod_id_'+prodId).text($('#first_time_pro_price_'+prodId).val());
                                $('#multiple_drop_down_'+prodId).removeClass('opened');
                                $('#price_list_'+prodId).hide(500);
                            }
                        });
                    }
                    else {
                        // Cart add or update message display
                        $('#cart_success_message').html('');
                        $('#cart_success_message').removeClass('alert-success');
                        $('#cart_success_message').addClass('alert-warning');
                        $('#cart_success_message').html('<strong>'+data.message+'</strong>');
                        $("#cart_success_message").slideDown(500).delay(1000);

                        setTimeout(function() {
                            $("#cart_success_message").slideUp(1000).delay(1000);
                        }, 100);
                    }        
                }
            });
            // Add to cart section END
        }else{
             if(type=='reloadpage'){
                location.reload();
             }
        }     


    }

    $(".add_to_basket").on('click', function () {
        var productId = $(this).data('productid');
        var prodId = $(this).data('prodid');    // without encryption
        var showIngredients = $(this).data('showingredients');
        var ingredientIds = '';
        var hasAttribute = $(this).data('hasattribute');
        var attributeId = $(this).data('attributeid');
        var specialId = $(this).data('specialid');
        var drinkId = $(this).data('drinkid');
        var menuValueIds = '';
        var isMenu = $(this).data('ismenu');
        var quantity = $('#tt_qtyInput_'+prodId).val();
        var po1=""; 
        localStorage.setItem('current_scroll_position',"");
        localStorage.setItem('current_scroll_position_load',"");
        if($(this).closest('.item_box').length){
            localStorage.setItem('current_scroll_position_load',$(this).closest('.item_box').offset().top);
        }
        if($(this).closest('.item_box').find('.opened').length){                
                po1=$(this).closest('.item_box').offset().top;
                //alert(po1);
                localStorage.setItem('current_scroll_position',po1);
        }
        var pinCode = $('#pincode').val();
        if (pinCode == '') {
            /**
             * modified by shanti info
             */
             if (productId != '' || specialId != '' || drinkId != '') {
                if (showIngredients != '') {
                    ingredientIds = $('#product_without_attribute_ingredient_ids_' + prodId).val();
                }
            
                if (isMenu == 'Y') {
                    // single dropdown
                    $('.dropdown_menu_value_' + prodId).each(function(index, value) {
                        if ($(this).val() != '') {
                            menuValueIds += $(this).val() + ',';
                        }
                    });
                    // multiple dropdown
                    $('.dropdown_menu_value_multiple_' + prodId + ':checkbox:checked').each(function() {
                        var checkedVal = (this.checked ? $(this).val() : "");
                        menuValueIds += checkedVal + ',';
                    });
            
                    // console.log(menuValueIds);
                }
                const mycardObject = {
                    productId: productId,
                    showIngredients: showIngredients,
                    ingredientIds: ingredientIds,
                    hasAttribute: hasAttribute,
                    attributeId: attributeId,
                    specialId: specialId,
                    drinkId: drinkId,
                    isMenu: isMenu,
                    menuValueIds: menuValueIds,
                    quantity: quantity
                }
                localStorage.setItem("holdCardData", JSON.stringify(mycardObject));
            }
            $('#pincode_modal').addClass('tt_modal_show');
            $('body').addClass('tt_modal_open');
        } else {

            if (productId != '' || specialId != '' || drinkId != '') {
                if (showIngredients != '') {
                    ingredientIds = $('#product_without_attribute_ingredient_ids_'+prodId).val();
                }
                
                if (isMenu == 'Y') {
                    // single dropdown
                    $('.dropdown_menu_value_'+prodId).each(function(index, value) {
                        if ($(this).val() != '') {
                            menuValueIds += $(this).val() + ',';
                        }
                    });
                    // multiple dropdown
                    $('.dropdown_menu_value_multiple_'+prodId+':checkbox:checked').each(function () {
                        var checkedVal = (this.checked ? $(this).val() : "");
                        menuValueIds += checkedVal + ',';
                    });
                    // console.log(menuValueIds);
                }

                $('body').addClass('clicked');
                if (ajax_check) {
                    return;
                }
                ajax_check = true;

                // Add to cart section START
                var addToCartUrl = websiteLink + '/' + siteLang + '/add-to-cart';
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: addToCartUrl,
                    method: 'POST',
                    cache: false,
                    data: {
                        productId: productId,
                        showIngredients: showIngredients,
                        ingredientIds: ingredientIds,
                        hasAttribute: hasAttribute,
                        attributeId: attributeId,
                        specialId: specialId,
                        drinkId: drinkId,
                        isMenu: isMenu,
                        menuValueIds: menuValueIds,
                        quantity: quantity,
                    },
                    success: function (cartResponse) {
                        ajax_check = false;
                        var data = jQuery.parseJSON(cartResponse);
                        
                        if (showIngredients != '') {
                            $('#product_without_attribute_ingredient_ids_'+prodId).val('');
                            $('#product_without_attribute_price_'+prodId).val(0);
                            $('.ingredients_checkbox_'+prodId).prop("checked", false);
                            $('#product_without_attribute_ingredient_price_'+prodId).html($('#product_previous_price_'+prodId).val());
                        }
                        // if attribute exist
                        if (hasAttribute == 'Y') {
                            $('.havingAttribute_'+prodId).each(function() {
                                $(this).text($(this).data('bp'));
                            });
                        }

                        $('body').removeClass('clicked');   // loader hide
                        if (data.type == 'success') {
                            $('#tt_qtyInput_'+prodId).val(1);

                            var getCartDataUrl = websiteLink + '/' + siteLang + '/get-cart-details';
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: getCartDataUrl,
                                method: 'GET',
                                data: {
                                },
                                success: function (cartDetailsResponse) {
                                    // $('body').removeClass('clicked');

                                    // Cart add or update message display
                                    $('#cart_success_message').html('');
                                    $('#cart_success_message').removeClass('alert-warning');
                                    $('#cart_success_message').addClass('alert-success');
                                    $('#cart_success_message').html('<strong>'+data.message+'</strong>');
                                    $("#cart_success_message").slideDown(500).delay(1000);

                                    $('#cartDetails').html(cartDetailsResponse.html);

                                    // if (cartDetailsResponse.totalCartPrice > 0) {
                                    if (cartDetailsResponse.productExist != 0) {
                                        if(!$('.mainBody').hasClass('withCartValue'))
                                            $('.mainBody').addClass('withCartValue');
                                        $('#mobile_cart_checkout').show();
                                        $('#mobile_cart_amount').html(cartDetailsResponse.totalCartPrice);
                                    }

                                    // Drinks items
                                    if (prodId != '') {
                                        $('#drinks_items').show(300);
                                    }

                                    // Minimum order section
                                    if (cartDetailsResponse.minOrderMessageStatus == 1) {
                                        $('#remaining_amount').html(cartDetailsResponse.remainingToAvoidMinimumOrder);
                                        $('#min_cart_div').show(500);
                                    } else {
                                        $('#min_cart_div').hide();
                                        $('#remaining_amount').html('');
                                    }
                                    clickCollectCheck();
                                    setTimeout(function() {
                                        $("#cart_success_message").slideUp(1000).delay(1000);
                                    }, 100);

                                    // reset to default
                                    $('.singleDropDown').prop('selectedIndex',0);
                                    $('.multipleDropDown').prop('checked', false);
                                    $('#updated_prod_id_'+prodId).text($('#first_time_pro_price_'+prodId).val());
                                    $('#multiple_drop_down_'+prodId).removeClass('opened');
                                    $('#price_list_'+prodId).hide(500);     
                                    BodyScrollCart();                               
                                }
                            });
                        }
                        else {
                            // Cart add or update message display
                            $('#cart_success_message').html('');
                            $('#cart_success_message').removeClass('alert-success');
                            $('#cart_success_message').addClass('alert-warning');
                            $('#cart_success_message').html('<strong>'+data.message+'</strong>');
                            $("#cart_success_message").slideDown(500).delay(1000);

                            setTimeout(function() {
                                $("#cart_success_message").slideUp(1000).delay(1000);
                            }, 100);
                        }        
                    }
                });
                // Add to cart section END
            }            
        }        
    });
    /************************************* Cart End ************************************/

    // Delete address
    $('.delete_address').on('click', function () {
        swal.fire({
			// title: 'Delete Address',
			text: 'Are you sure you want to delete this Address?',
            icon: 'warning',
            allowOutsideClick: false,
            // confirmButtonClass: "swal_confirm",
            // cancelButtonClass: "swal_cancel",
            confirmButtonColor: '#1279cf',
            cancelButtonColor: '#333333',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes',
            // closeOnConfirm: false,
		}).then((result) => {
			if (result.value) {
                var siteLink = $('#site_link').val();
                var siteLang = $('#site_lang').val();
    
                var addressId = $(this).data('addressid');
                var addrId = $(this).data('addrid');
        
                $('body').addClass('clicked');
                if (ajax_check) {
                    return;
                }
                ajax_check = true;
                var deleteAddressUrl = siteLink + '/' + siteLang + '/users/delete-address';
                                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: deleteAddressUrl,
                    method: 'POST',
                    data: {
                        addressId: addressId,
                    },
                    success: function (deleteResponse) {
                        $('body').removeClass('clicked');
                        ajax_check = false;

                        var deleteAddressResponse = jQuery.parseJSON(deleteResponse);
                        if (deleteAddressResponse.type == 'success') {
                            swal.fire({
                                // title: deleteAddressResponse.title,
                                text: deleteAddressResponse.message,
                                icon: deleteAddressResponse.type,
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                            $('#address_'+addrId).remove();
                        } else {
                            swal.fire({
                                // title: deleteAddressResponse.title,
                                text: deleteAddressResponse.message,
                                icon: deleteAddressResponse.type,
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                        }
                    }
                });
            }
		});

    });

    // Change avatar
    $(".update_avatar").on('click', function () {
        var siteLink = $('#site_link').val();
        var siteLang = $('#site_lang').val();
        var avatarId = $(this).data('id');
        
        if (avatarId != '') {
            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }

            ajax_check = true;
            var avatarUpdateUrl = siteLink + '/' + siteLang + '/users/change-avatar';
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: avatarUpdateUrl,
                method: 'POST',
                data: {
                    avatarId: avatarId,
                },
                success: function (avatarUpdateResponse) {
                    var response = jQuery.parseJSON(avatarUpdateResponse);
                    console.log(response.updatedAvatar);
                    ajax_check = false;
                    $('.avatar_update').css('background-image', 'url(' + response.updatedAvatar + ')');
                    $('body').removeClass('clicked');
                }
            });
        }
        
    });

    // Pin code checking
    $("#pinCodeForm").validate({
        rules: {
            pin_code: {
                required: true,
            },
        },
        messages: {
            pin_code: {
                required: "Please enter pin code",
            },
        },
        submitHandler: function(form) {
            // $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            $('#pin_code_available_message').css('visibility','hidden');
            ajax_check = true;
            var pinCodeCheckingUrl = websiteLink + '/' + siteLang + '/pin-code-availability';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: pinCodeCheckingUrl,
                method: 'POST',
                data: {
                    pinCode: $('#pin_code').val(),
                },
                dataType: 'json',
                success: function (pinCodeResponse) {
                    ajax_check = false;
                    // $('body').removeClass('clicked');
                    
                    if (pinCodeResponse.type == 'success') {   
                        $('#pin_code_available_message').removeClass('errorMessage');
                        $('#pin_code_available_message').addClass('successMessage');
                        // $('#pin_code_available_message').html(pinCodeResponse.message).show();
                        // setTimeout(function(){ window.location.reload(); }, 1500);
                        addToCardHoldData('reloadpage');
                        //window.location.reload();
                    } else {
                        $('#pin_code_available_message').css('visibility','visible');
                        $('#pin_code_available_message').removeClass('successMessage');
                        $('#pin_code_available_message').addClass('errorMessage');
                        $('#pin_code_available_message').html(pinCodeResponse.message);
                        $('#pin_code_available_message').show();
                        //setTimeout(function(){ $('#pin_code_available_message').css('visibility','hidden'); }, 3000);
                    }
                }
            });
        }
    });

    // Checkout Form - Place Order
    $("#placeOrderForm").validate({
        ignore: [],
        rules: {
            delivery_time: {
                required: true,
            },
            phone_no: {
                required: true,
            },
            addressAlias: {
                required: true,
            },
            delivery_address: {
                required: true,
            },
            // checkout_message: {
            //     required: true,
            // },
        },
        messages: {
            delivery_time: {
                required: "Please select delivery time",
            },
            phone_no: {
                required: "Please enter contact number",
            },
            addressAlias: {
                required: "Please select address",
            },
            delivery_address: {
                required: "Please add an address",
            },
            // checkout_message: {
            //     required: "Please enter message",
            // },
        },
        submitHandler: function(form) {
            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }

            ajax_check = true;
            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();
            // Checking Available Timings respective to day
            var checkingAvailableSlotUrl = siteLink + '/' + siteLang + '/checking-restaurant-slot-availability';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: checkingAvailableSlotUrl,
                method: 'POST',
                data: {
                    delivery_time: $('#delivery_time').val(),
                },
                success: function (availableSlotResponse) {
                    var finalResponse = jQuery.parseJSON(availableSlotResponse);
                    // console.log(finalResponse.type);

                    if (finalResponse.type == 'error') {
                        ajax_check = false;
                        $('body').removeClass('clicked');
                        swal.fire({
                            // title: finalResponse.title,
                            text: finalResponse.message,
                            icon: finalResponse.type,
                            allowOutsideClick: false,
                            confirmButtonColor: '#1279cf',
                            cancelButtonColor: '#333333',
                            showCancelButton: false,
                            confirmButtonText: 'Ok',
                            // closeOnConfirm: false,
                        });
                    } else {
                        // order place
                        form.submit();
                    }
                }
            });
        }
    });

    // close alert section
    $('.close_alert_box').on('click', function() {
        $('.alert-dismissable').hide(1000);
    });
    
    /* Guest Checkout Form Start */
    $("#guestCheckoutForm").validate({
        rules: {
            delivery_date: {
                required: true,
            },
            delivery_time: {
                required: true,
            },
            full_name: {
                required: true,
            },
            phone_no: {
                required: true,
            },
            email: {
                required: true,
                valid_email: true,
            },
            // company: {
            //     required: true,
            // },
            street: {
                required: $('.clickandsend_option').length <= 0,
            },
            // floor: {
            //     required: true,
            // },
            // door_code: {
            //     required: true,
            // },
            post_code: {
                required: true,
            },
            city: {
                required: $('.clickandsend_option').length <= 0,
            },
            addressAlias: {
                required: $('.clickandsend_option').length <= 0,
            },
            city: {
                required: $('.clickandsend_option').length <= 0,
            },
            payment_method: {
                required: true,
            },            
        },
        messages: {
            delivery_date: {
                required: "Please select delivery date",
            },
            delivery_time: {
                required: "Please select delivery time",
            },
            full_name: {
                required: "Please enter first name & last name",
            },
            phone_no: {
                required: "Please enter contact number",
            },
            email: {
                required: "Please enter email",
                valid_email: "Please enter valid email",
            },
            // company: {
            //     required: "Please enter company or c/o",
            // },
            street: {
                required: "Please enter street and number",
            },
            // floor: {
            //     required: "Please enter floor",
            // },
            // door_code: {
            //     required: "Please enter door code",
            // },
            post_code: {
                required: "Please enter post code",
            },
            city: {
                required: "Please enter city",
            },
            addressAlias: {
                required: "Please select address type",
            },
            payment_method: {
                required: "Please select payment mode",
            },            
        }, errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {


            $(document).find('#full_name-error').remove();        
            $(document).find('#full_name-error-1').remove();
            //var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
            var name = document.getElementById('full_name').value;
            var checkname=name.trim();
            checkname=checkname.split(" ");        
            if(checkname.length<2){                 
                $('#full_name').after('<div id="full_name-error-1" class="error">Vorname & Nachname eingeben</div>')
                return false;
            }       

            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();

            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            ajax_check = true;
            var guestCheckoutUrl = siteLink + '/' + siteLang + '/place-order';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: guestCheckoutUrl,
                method: 'POST',
                data: $('#guestCheckoutForm').serialize(),
                dataType: 'json',
                success: function (response) {
                    ajax_check = false;
                    // console.log(response);

                    if (response.type == 'success') {
                        // $('#integrate_payment_form').html('');

                        if ($('input[name="payment_method"]:checked').val() == 4) {
                            loadmodalPayrex();
                           // window.location.href = siteLink+'/'+siteLang+'/payByPayrex';
                        }else{

                        if ($('input[name="payment_method"]:checked').val() == 2) {
                            console.log(response.payment);
                            // $('#integrate_payment_form').html(response.payment);

                            // $('.goToPayment').attr('disabled',true);
                            // $('.returnToRestaurant').attr('style', 'pointer-events: none;');

                            // $(document).on('DOMNodeRemoved', '.stripe_checkout_app', guestStripeClose);
                            
                            // $('button#customButton').trigger('click');

                            // $('body').removeClass('clicked');
                                $('#stripe_modal').addClass('tt_modal_show'); 
                                $('#stripe_name').val($('#full_name').val());
                                $('body').addClass('tt_modal_open'); 
                                $('body').removeClass('clicked');
                        } else {
                            $('.goToPayment').attr('disabled',true);
                            $('.returnToRestaurant').attr('style', 'pointer-events: none;');
                             $('body').removeClass('clicked');

                            if (response.redirectPage == 'home') {
                                window.location.replace(siteLink + '/' + siteLang);
                            } else if (response.redirectPage == 'thank-you') {
                                window.location.replace(siteLink + '/' + siteLang + '/thank-you/' + response.oId);
                            }
                        }
                     }
                    } else {
                        $('body').removeClass('clicked');
                        if(response.type=='refetch_sloat'){
                            swal.fire({
                                // title: response.title,
                                text: response.message,
                                icon: 'error',
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                            refetchDeliveryTimeSloat();
                       }else{
                            swal.fire({
                                // title: response.title,
                                html: response.message,
                                icon: response.type,
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                       }
                    }
                }
            });
        }
    });
    /* Guest Checkout Form End */


       /**
     * refetch time sloat
     */
        function refetchDeliveryTimeSloat(){



            $('#is_as_soon_as_possible').val('');
            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();

            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }

            ajax_check = true;
            var checkoutUrl = siteLink + '/' + siteLang + '/get-delivery-slots';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: checkoutUrl,
                method: 'POST',
                data: {
                    delivery_date: $(document).find('#delivery_date').val()
                },
                success: function (response) {
                    ajax_check = false;
                    $('#delivery_time').html(response);
                    $('body').removeClass('clicked');
                    $('#checkoutForm').submit();
                    loadFirstSelectInTime();
                    //sp2
                }
            });



}


    /* Checkout Form Start */
    $("#checkoutForm").validate({
        ignore: [],
        rules: {
            delivery_date: {
                required: true,
            },
            delivery_time: {
                required: true,
            },
            phone_no: {
                required: true,
            },
            payment_method: {
                required: true,
            },
            addressAlias: {
                required: $('.clickandsend_option').length <= 0,
            },
            delivery_address: {
                required: $('.clickandsend_option').length <= 0,
            },
            // checkout_message: {
            //     required: true,
            // },
        },
        messages: {
            delivery_date: {
                required: "Please select delivery date",
            },
            delivery_time: {
                required: "Please select delivery time",
            },
            phone_no: {
                required: "Please enter contact number",
            },
            payment_method: {
                required: "Please select payment mode",
            },
            addressAlias: {
                required: "Please select address",
            },
            delivery_address: {
                required: "Please add an address",
            },
            // checkout_message: {
            //     required: "Please enter message",
            // },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();

            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }

            ajax_check = true;
            var checkoutUrl = siteLink + '/' + siteLang + '/place-order';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: checkoutUrl,
                method: 'POST',
                data: $('#checkoutForm').serialize(),
                dataType: 'json',
                cache: false,
                success: function (response) {
                    // $('body').removeClass('clicked');
                    ajax_check = false;

                    if (response.type == 'success') {
                        $('#integrate_payment_form').html('');
                        if ($('input[name="payment_method"]:checked').val() == 4) {
                            loadmodalPayrex();
                           // window.location.href = siteLink+'/'+siteLang+'/payByPayrex';
                        }else{ 
                            if ($('input[name="payment_method"]:checked').val() == 2) {
                                // $('#integrate_payment_form').html(response.payment);

                                // $('button#customButton').trigger('click');

                                // $('body').removeClass('clicked');
                                //console.log(response.payment);
                                $('#stripe_modal').addClass('tt_modal_show'); 
                                $('#stripe_name').val($('#full_name').val());
                                $('body').addClass('tt_modal_open'); 
                                $('body').removeClass('clicked');
                            } else {
                                $('body').removeClass('clicked');
                                if (response.redirectPage == 'home') {
                                    window.location.replace(siteLink + '/' + siteLang);
                                } else if (response.redirectPage == 'orders-reviews') {
                                    window.location.replace(siteLink + '/' + siteLang + '/users/orders-reviews');
                                } else if (response.redirectPage == 'thank-you') {
                                    window.location.replace(siteLink + '/' + siteLang + '/thank-you/' + response.oId);
                                }
                            }
                      }
                    } else {
                        $('body').removeClass('clicked');
                        if(response.type=='refetch_sloat'){

                            swal.fire({
                                // title: response.title,
                                text: response.message,
                                icon: 'error',
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                            refetchDeliveryTimeSloat();
                       }else{
                            swal.fire({
                                // title: response.title,
                                html: response.message,
                                icon: response.type,
                                allowOutsideClick: false,
                                confirmButtonColor: '#1279cf',
                                showCancelButton: false,
                                confirmButtonText: 'Ok',
                            });
                     }
                    }
                }
            });
        }
    });
    /* Checkout Form End */

    // Getting datewise delivery slosts
    $('.getDeliverySlots').datepicker({
        changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy",
		minDate: 0,
        onSelect: function() {
            $('#is_as_soon_as_possible').val('');
            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();

            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }

            ajax_check = true;
            var checkoutUrl = siteLink + '/' + siteLang + '/get-delivery-slots';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: checkoutUrl,
                method: 'POST',
                data: {
                    delivery_date: this.value
                },
                success: function (response) {
                    ajax_check = false;
                    var vals=$(document).find('#delivery_time').val();
                    $('#delivery_time').html(response);
                    $('body').removeClass('clicked');
                    $(document).find('#delivery_time').find('option').each(function(){
                        if($(this).text()==vals){
                           $('#delivery_time').val(vals);
                        }
                   })
                   loadFirstSelectInTime();
                   //sp2
                }
            });
        }
    });

    // First time phone number save from checkout page
    $('#add_new_address').on('click', function() {
        var website         = $('#site_link').val();
        var currentSiteLang = $('#site_lang').val();
        var inputPhoneNumber= $('#phone_no').val();
        if (inputPhoneNumber == '') {
            window.location.href = website + '/' + currentSiteLang + '/checkout-add-address';
        } else {
            window.location.href = website + '/' + currentSiteLang + '/checkout-add-address?cno='+inputPhoneNumber;
        }        
    });

    // Apply coupon
    $('#apply-coupon').on('click', function() {
        $('#coupon_apply_remove_message').html('');
        var couponCode      = $('#coupon_code').val();
        var deliveryCharge  = $('#delvry_chrg').val();
        var netPay          = $('#net_pay').val();
        var cardAmount      = $('#card_amount').val();
        var paymentMethod   = $('input[name="payment_method"]:checked').val();
        $('#disc_amount').val(0);

        if (couponCode != '') {
            var siteLink = $('#site_link').val();
            var siteLang = $('#site_lang').val();
            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            ajax_check = true;
            var applyCouponUrl = siteLink + '/' + siteLang + '/apply-coupon';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: applyCouponUrl,
                method: 'POST',
                data: {
                    coupon_code: couponCode,
                    delivery_charge: deliveryCharge,
                    card_amount: cardAmount,
                    payment_method: paymentMethod,
                },
                dataType: 'json',
                success: function (response) {
                    $('body').removeClass('clicked');
                    ajax_check = false;                    
                    $('#coupon_apply_remove_message').html(response.msg);
                    if (response.has_error == 0) {
                        $('#coupon_apply_remove_message').removeClass('errorMessage');
                        $('#coupon_apply_remove_message').addClass('successMessage');
                        $('#discountAmountSection').show();     // Discount amount div show
                        $('#discount').text('CHF -'+response.discount_amount);  // Discount amount show
                        $('#disc_amount').val(response.discount_amount);
                        $(document).find('.final_amount_show').text(response.net_payable_amount);
                        $('#remove_coupon').show();
                    } else {
                        $('#coupon_code').val('');
                        $('#coupon_apply_remove_message').removeClass('successMessage');
                        $('#coupon_apply_remove_message').addClass('errorMessage');
                        $('#discountAmountSection').hide();     // Discount amount div hide
                        $('#discount').text('');  // Discount amount hide
                        $('#remove_coupon').hide();
                    }

                    $('#net_payable_amount').val(response.net_payable_amount);
                    $('#netPayableWithDelivery').text('CHF '+response.net_payable_amount); // net payable amount show
                    $('#card_amount').val(response.card_amount);
                    $('#cardPaymentAmount').text('CHF '+ response.card_amount);
                    $('#coupon_apply_remove_message').show();
                    setTimeout(function(){ $('#coupon_apply_remove_message').hide(); }, 5000);

                    // Load stripe payment form
                    $('#guestPaymentForm').html(response.payment_form);
                    $('#paymentForm').html(response.payment_form);
                }
            });
        } else {
            $('#remove_coupon').hide();
            $('#discountAmountSection').hide();
            $('#netPayableWithDelivery').text('CHF '+netPay); // net payable amount show
            $('#net_payable_amount').val(netPay); // net payable amount show
            $('#card_amount').val(cardAmount.toFixed(2));
            $('#cardPaymentAmount').text('CHF '+ cardAmount.toFixed(2));
            $('#coupon_apply_remove_message').show();
            $('#coupon_apply_remove_message').removeClass('successMessage');
            $('#coupon_apply_remove_message').addClass('errorMessage');
            $('#coupon_apply_remove_message').html('Please enter coupon code');
        }
    });

    // If Card payment selected
    $('.paymentMethod').on('click', function() {
        $('#card_amount').val(0);
        var deliveryCharge  = $('#delvry_chrg').val();
        var netPay          = $('#net_pay').val();
        var netPayableAmount= $('#net_payable_amount').val();
        var discAmount      = $('#disc_amount').val();
        var siteLink        = $('#site_link').val();
        var siteLang        = $('#site_lang').val();

        $('body').addClass('clicked');
        if (ajax_check) {
            return;
        }
        ajax_check = true;

        if ($('input[name="payment_method"]:checked').val() == 2 || $('input[name="payment_method"]:checked').val() == 4) {
            var calculateCardAmountUrl = siteLink + '/' + siteLang + '/calculate-card-amount';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: calculateCardAmountUrl,
                method: 'POST',
                data: {
                    delivery_charge: deliveryCharge,
                    net_pay: netPay,
                    net_payable_amount: netPayableAmount,
                    discount_Amount: discAmount
                },
                dataType: 'json',
                success: function (response) {
                    $('body').removeClass('clicked');
                    ajax_check = false;                    
                    if (response.has_error == 0) {
                        $('#cardPaymentAmount').text('CHF '+ response.card_amount);
                        $('#card_amount').val(response.card_amount);
                        $('#net_payable_amount').val(response.net_payable_amount);
                        $('#netPayableWithDelivery').text('CHF '+response.net_payable_amount);
                        $('#cardPaymentAmountSection').show();
                        $(document).find('.final_amount_show').text(response.net_payable_amount);
                    } else {
                        $('#cardPaymentAmount').text('CHF 0.00');
                        $('#cardPaymentAmountSection').hide();

                        $('#net_payable_amount').val(response.netPay);
                        $('#netPayableWithDelivery').text('CHF '+response.netPay);
                    }
                    
                    // Load stripe payment form
                    $('#guestPaymentForm').html(response.payment_form);
                    $('#paymentForm').html(response.payment_form);
                }
            });
        } else {
            var calculateCardAmountUrl = siteLink + '/' + siteLang + '/regenerate-stripe-form';

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: calculateCardAmountUrl,
                method: 'POST',
                data: {
                    delivery_charge: deliveryCharge,
                    net_pay: netPay,
                    net_payable_amount: netPayableAmount,
                    discount_Amount: discAmount
                },
                dataType: 'json',
                success: function (response) {
                    $('body').removeClass('clicked');
                    ajax_check = false;

                    // $('#netPayableWithDelivery').text('CHF '+ response.net_payable_amount.toFixed(2));
                    // $('#net_payable_amount').val(response.net_payable_amount.toFixed(2));

                    $('#netPayableWithDelivery').text('CHF '+ response.net_payable_amount);
                    $(document).find('.final_amount_show').text(response.net_payable_amount);
                    $('#net_payable_amount').val(response.net_payable_amount);
                    
                    // Load stripe payment form
                    $('#guestPaymentForm').html(response.payment_form);
                    $('#paymentForm').html(response.payment_form);
                }
            });
            
            $('#cardPaymentAmount').text('CHF 0.00');
            $('#cardPaymentAmountSection').hide();

            // $('#net_payable_amount').val(netPay);
            // var netPayAmnt = parseFloat(netPay) - parseFloat(discAmount);
            // $('#netPayableWithDelivery').text('CHF '+ netPayAmnt.toFixed(2));
            // $('#net_payable_amount').val(netPayAmnt.toFixed(2));
        }
    });

    // Set hidden field for as soon as possible section
    var asSoon = '';
    asSoon = $("#delivery_time option:selected").data("assoon");
    $('#is_as_soon_as_possible').val(asSoon);
    $(document).on('change', '#delivery_time', function() {
        $('#is_as_soon_as_possible').val('');
        var asSoon = $("#delivery_time option:selected").data('assoon');
        $('#is_as_soon_as_possible').val(asSoon);
    });

});

// One Page load show cart details start
window.onload = function() {
    var getCart = $('#get_cart').val();
    if (getCart != 0) {
        getCartDetails();
    }    
};
// One Page load show cart details end

// Reload the page when Stripe button closed and Guest logged in
function guestStripeClose() {
    $('.goToPayment').removeAttr('disabled');
    $('.returnToRestaurant').removeAttr('style');
}

// function to get cart details
function getCartDetails() {
   
    var websiteLink = $('#website_link').val();
    var siteLang = $('#website_lang').val();
    var ajax_check = false;

    // $('body').addClass('clicked');
    if (ajax_check) {
        return;
    }

    ajax_check = true;
    var getCartDataUrl = websiteLink + '/' + siteLang + '/get-cart-details';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: getCartDataUrl,
        method: 'GET',
        data: {
        },
        success: function (cartDetailsResponse) {
            ajax_check = false;
            // console.log(cartDetailsResponse);
            // $('body').removeClass('clicked');
            
            // If at least one product exist in cart then show drinks section
            if (cartDetailsResponse.productExist == 1) {
                $('#drinks_items').show();
            } else {
                $('#drinks_items').hide();
            }

            // If total order amount is less that Helper::MINIMUM_ORDER_AMOUNT = 20 then show message
            if (cartDetailsResponse.minOrderMessageStatus == 1) {
                $('#remaining_amount').html(cartDetailsResponse.remainingToAvoidMinimumOrder);
                $('#min_cart_div').show(500);
            } else {                
                $('#min_cart_div').hide();
                $('#remaining_amount').html('');
            }
            
            $('#cartDetails').html(cartDetailsResponse.html);

            //alert(cartDetailsResponse.productExist);

            // if (cartDetailsResponse.totalCartPrice == 0) {
            if (cartDetailsResponse.productExist == 0) {
                $('#mobile_cart_checkout').hide();
                // $('#mobile_cart_amount').html('');
                $('#mobile_cart_amount').html(cartDetailsResponse.totalCartPrice);
            } else {
                $('#mobile_cart_checkout').show();
                $('#mobile_cart_amount').html(cartDetailsResponse.totalCartPrice);
            }

            clickCollectCheck();
        }
    });
}

// function to get delivery charge from selected address
function getSelectedAddressWiseDeliveryCharge(selectedAddressId) {
    var websiteLink     = $('#website_link').val();
    var siteLang        = $('#website_lang').val();
    var totalPrice      = $('#totalPrice').val();
    var netPay          = $('#net_pay').val();
    var netPayableAmount= $('#net_payable_amount').val();
    var discAmount      = $('#disc_amount').val();
    var cardAmount      = $('#card_amount').val();
    var paymentMethod   = $('input[name="payment_method"]:checked').val();

    if (selectedAddressId != '') {
        var getDeliveryChargeUrl = websiteLink + '/' + siteLang + '/get-delivery-charge';        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: getDeliveryChargeUrl,
            method: 'POST',
            data: {
                selectedAddressId: selectedAddressId,
                net_pay: netPay,
                net_payable_amount: netPayableAmount,
                discount_Amount: discAmount,
                card_amount: cardAmount,
                payment_method: paymentMethod,
            },
            success: function (response) {
                var responseData = jQuery.parseJSON(response);

                if (responseData.pinCodeWiseDeliveryCharge != 0) {
                    $('#only_delivery_section').show();
                } else {
                    $('#only_delivery_section').hide();
                }
                
                $('#deliveryCharge').text('CHF '+responseData.pinCodeWiseDeliveryCharge);
                $('#delivery_charge').val(responseData.pinCodeWiseDeliveryCharge);
                $('#delvry_chrg').val(responseData.pinCodeWiseDeliveryCharge);
                
                $('#totalPriceWithDelivery').text('CHF '+responseData.total_amount);

                $('#netPayableWithDelivery').text('CHF '+responseData.net_payable_amount); // net payable amount show
                $('#net_payable_amount').val(responseData.net_payable_amount);
                $('#card_amount').val(responseData.card_amount);
                $('#cardPaymentAmount').text('CHF '+ responseData.card_amount);

                // Load stripe payment form
                $('#guestPaymentForm').html(responseData.payment_form);
                $('#paymentForm').html(responseData.payment_form);
            }
        });
    } else {
        swal.fire({
            text: 'Something went wrong, please try again later.',
            icon: 'error',
            allowOutsideClick: false,
            confirmButtonColor: '#1279cf',
            showCancelButton: false,
            confirmButtonText: 'Ok',
        });
    }
}

function sweetalertMessageRender(target, message, type, confirm = false) {
    let options = {
        title: '',
        text: message,
        icon: type,
        type: type,
        confirmButtonColor: '#144B8B',
        cancelButtonColor: '#02C402',
    };
    if (confirm) {
        options['showCancelButton'] = true;
        options['cancelButtonText'] = 'Cancel';
        options['confirmButtonText'] = 'Yes';
    }
    return Swal.fire(options).then((result) => {
        if (confirm == true && result.value) {
            window.location.href = target.getAttribute('data-href'); 
        } else {
            return (false);
        }
    });
}

function sweetAlertRemoveCoupon(target, message, type, confirm = false) {
    let options = {
        title: '',
        text: message,
        icon: 'warning',
        confirmButtonColor: '#1279cf',
        cancelButtonColor: '#333333',
    };
    if (confirm) {
        options['showCancelButton'] = true;
        options['cancelButtonText'] = 'Cancel';
        options['confirmButtonText'] = 'Yes';
    }
    return Swal.fire(options).then((result) => {
        if (confirm == true && result.value) {
            var siteLink        = $('#site_link').val();
            var siteLang        = $('#site_lang').val();
            var deliveryCharge  = $('#delvry_chrg').val();
            var paymentMethod   = $('input[name="payment_method"]:checked').val();
            var ajax_check      = false;
            $('#disc_amount').val(0);

            $('#coupon_apply_remove_message').html('');
            $('#coupon_apply_remove_message').removeClass('coupon_renew_success');
            $('#coupon_apply_remove_message').removeClass('coupon_renew_error');
            
            var removeCouponUrl = siteLink + '/' + siteLang + '/remove-coupon';

            $('body').addClass('clicked');
            if (ajax_check) {
                return;
            }
            ajax_check = true;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: removeCouponUrl,
                method: 'POST',
                data: {
                    coupon_code : $('#coupon_code').val(),
                    delivery_charge : deliveryCharge,
                    payment_method : paymentMethod
                },
                dataType: 'json',
                success: function (response) {
                    $('body').removeClass('clicked');
                    ajax_check = false;

                    $('#coupon_apply_remove_message').html(response.msg);
                    $('#coupon_code').val('');

                    if (response.has_error == 0) {                        
                        $('#coupon_apply_remove_message').removeClass('errorMessage');
                        $('#coupon_apply_remove_message').addClass('successMessage');
                    } else {
                        $('#coupon_apply_remove_message').removeClass('successMessage');
                        $('#coupon_apply_remove_message').addClass('errorMessage');
                    }
                    $('#discountAmountSection').hide();     // Discount amount div hide
                    $('#discount').text('');  // Discount amount hide
                    $('#netPayableWithDelivery').text('CHF '+response.net_payable_amount); // net payable amount show
                    $('#net_payable_amount').val(response.net_payable_amount);
                    $('#card_amount').val(response.card_amount);
                    $('#cardPaymentAmount').text('CHF '+response.card_amount);
                    $(document).find('.final_amount_show').text(response.net_payable_amount);     
                    $('#remove_coupon').hide();
                    $('#coupon_apply_remove_message').show();
                    setTimeout(function(){ $('#coupon_apply_remove_message').hide(); }, 5000);

                    // Load stripe payment form
                    $('#guestPaymentForm').html(response.payment_form);
                    $('#paymentForm').html(response.payment_form);
                }
            });
        } else {
            return (false);
        }
    });
}


function isHomePage(){
    if($(document).find('.is-home-page-class').length > 0){
        return true;
    }
    return false;
}


function BodyScrollCart(){   
    setTimeout(function () {
            scrollpostionwindow=localStorage.getItem('current_scroll_position');
            if(scrollpostionwindow && isHomePage()){
                    //alert(scrollpostionwindow);
                    $('html, body').animate({
                        'scrollTop' : Number(scrollpostionwindow)-100
                    });   
            } 
            setTimeout(function () {
                localStorage.removeItem('current_scroll_position');
            }, 20);
    }, 200);
}
window.addEventListener("load", function(){
    
    setTimeout(function () {
            /**
             * Check for pincode option
             */
            current_scroll_position_load=localStorage.getItem('current_scroll_position_load');
            cartactionpincode=localStorage.getItem('cartactionpincode');
            if(current_scroll_position_load && cartactionpincode && isHomePage()){
                $('html, body').animate({
                    'scrollTop' : Number(current_scroll_position_load)-100
                });  
            }
            setTimeout(function () {
                localStorage.removeItem('current_scroll_position');
                localStorage.removeItem('current_scroll_position_load');
            }, 20);
    }, 700); 

    setTimeout(function () {
        /**
         * Check for delivery option
         */
        if($(document).find('#delivery_option').length){
            $(document).find('#delivery_option').val('Click & Collect');   
            if($(document).find('.deliverySwitch').find('input[type="checkbox"]').prop('checked')){
            $(document).find('#delivery_option').val('Delivery');   
            }
            clickCollectCheck();
        }
    }, 20);
    loadFirstSelectInTime();
    //sp2
    if($('#guestCheckoutForm').length || $('#checkoutForm').length){
        updatesetCheckoutFormValue();
    }
})

/**
 * Check select option in time
 */
//sp2
function loadFirstSelectInTime(){
    var attr='';
    if($(document).find('#delivery_time').length){
        var i=1;        
        $(document).find('#delivery_time option').each(function(){
            attr = $(this).attr('data-assoon');
            $(this).addClass('time-option-'+i);
            if (typeof attr !== 'undefined' && attr !== false) {
                if(i==2){
                     //alert(i)
                     $('.time-option-1').hide();
                }
            }
            i++;
        })
    }
}

/**
 * Hide min amount option for click collect
 */
 function clickCollectCheck(delivery_option=''){
    if(delivery_option==''){
         delivery_option=$(document).find('#delivery_option').val();
    } 
    $(document).find('.min-order-value-emty-cart').show();
    var checkmin=$(document).find('#remaining_amount').html();
    if(checkmin){
            $(document).find('#min_cart_div').show();
            if(delivery_option=='Click & Collect'){
                $(document).find('.min-order-value-emty-cart').hide();
                $(document).find('#min_cart_div').hide();
            }
    }else{
        if(delivery_option=='Click & Collect'){
            $(document).find('.min-order-value-emty-cart').hide();
        }
    }
    //$('.sidebar_right').find('.theiaStickySidebar').css('position','static'); 
 }

 function loadmodalPayrex(){
    
    $(document).find('.payrexx-modal-window').remove();
    var siteLink  = $('#website_link').val();
    var siteLang = $('#website_lang').val();
    var siteurlPayment= siteLink+'/'+siteLang+'/payByPayrex';
    $.post(siteurlPayment,function(dataResponse){
        if(dataResponse=='error'){
            location.reload();
        }else{
            
            
            $('body').append('<a href="#" class="payrexx-modal-window"  data-href="'+dataResponse+'" style="display:none">pay</a>');
            
            $('body').removeClass('clicked');
            setTimeout(function(){
                loadmodalPayrexModel();
             }, 150);
          
        }
    })
}
   
/***************************************************************************************
************************Payrex Payment Gatway
****************************************************************************************/
var siteLink  = $('#website_link').val();
var siteLang = $('#website_lang').val();
function loadmodalPayrexModel(){
    $(document).find(".payrexx-modal-window").payrexxModal({
        hideObjects: ['#contact-details', '.contact','#header','.product-section','.payment-details'],
        hidden: function(transaction) {
            if (typeof transaction === 'object') {
                if (transaction.status === 'confirmed') {      
                    console.log(transaction);
                    redirectToThankYouPage(transaction.referenceId);
                    // window.location.replace(siteLink+'/'+siteLang+'/payrex-payment-sucess?transaction_id='+transaction.id);
                }else{
                    //localStorage.setItem('cardcleardata','');
                } 
            }
        }
    });
    $(document).find(".payrexx-modal-window").click();
}

// Redirection after payment success
function redirectToThankYouPage(transactionReferenceId) {
    var websiteLink = $('#website_link').val();
    var siteLang = $('#website_lang').val();
    
    if (transactionReferenceId != '') {
        var ajax_check = false;
        $('body').addClass('clicked');

        if (ajax_check) {
            return;
        }
        ajax_check = true;
        var ingredientsWithProductPriceUrl = websiteLink + '/' + siteLang + '/payrex-payment-redirect-thank-you-page';
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: ingredientsWithProductPriceUrl,
            method: 'POST',
            data: {
                orderId: transactionReferenceId,
            },
            success: function (uniqueOrderId) {
                $('body').removeClass('clicked');   // loader hide
                ajax_check = false;
                
                window.location.replace(websiteLink + '/' + siteLang + '/thank-you/' + uniqueOrderId);
            }
        });
    } else {
        window.location.replace(websiteLink + '/' + siteLang + '/');
    }  
}
 
 

/***************************************************************************************
************************On Language change store form value
****************************************************************************************/
function setCheckoutFormValue(){
     if($('#guestCheckoutForm').length){
        localStorage.setItem('check_out_phone_no',$('#phone_no').val());
        localStorage.setItem('check_out_delivery_time',$('#delivery_time').val()); 
        localStorage.setItem('check_out_email',$('#email').val());
        localStorage.setItem('check_out_full_name',$('#full_name').val());
        if($('#company').length){
            localStorage.setItem('check_out_company',$('#company').val());
        }
        if($('#street').length){
            localStorage.setItem('check_out_street',$('#street').val());
        }
        if($('#floor').length){
            localStorage.setItem('check_out_floor',$('#floor').val());
        }
        if($('#door_code').length){
            localStorage.setItem('check_out_door_code',$('#door_code').val());
        }
        if($('#city').length){
            localStorage.setItem('check_out_city',$('#city').val());
        }
        localStorage.setItem('check_out_paymentMethod',$('.paymentMethod:checked').val());
     }
     if($('#checkoutForm').length){
        localStorage.setItem('check_out_paymentMethod',$('.paymentMethod:checked').val());
        localStorage.setItem('check_out_delivery_time',$('#delivery_time').val()); 
        localStorage.setItem('check_out_phone_no',$('#phone_no').val());
        localStorage.setItem('check_out_full_name',$('#full_name').val());
        if($('.addressAlias').length){
            localStorage.setItem('check_out_addressAlias',$('.addressAlias:checked').val());
        }
     }
}
/***************************************************************************************
************************On language Change fill form value
****************************************************************************************/
function updatesetCheckoutFormValue(){
    if($('#checkoutForm').length){
            var check_out_phone_no=localStorage.getItem('check_out_phone_no')
            if(check_out_phone_no){
            $('#phone_no').val(check_out_phone_no);
              localStorage.removeItem('check_out_phone_no');
            }
            var check_out_full_name=localStorage.getItem('check_out_full_name')
            if(check_out_full_name){
                $('#full_name').val(check_out_full_name);
                localStorage.removeItem('check_out_full_name');
            }
            var check_out_paymentMethod=localStorage.getItem('check_out_paymentMethod')
            if(check_out_paymentMethod){
                $(document).find('.paymentMethod[value="'+check_out_paymentMethod+'"]').click(); 
                localStorage.removeItem('check_out_paymentMethod');
            } 
            var check_out_addressAlias=localStorage.getItem('check_out_addressAlias')
            if(check_out_addressAlias){
                $(document).find('.addressAlias[value="'+check_out_addressAlias+'"]').click(); 
                localStorage.removeItem('check_out_addressAlias');
            } 
        var check_out_delivery_time=localStorage.getItem('check_out_delivery_time')
        if(check_out_delivery_time){
          $('#delivery_time').val(check_out_delivery_time);
          localStorage.removeItem('check_out_delivery_time');
        } 
     }
    if($('#guestCheckoutForm').length){
        var check_out_phone_no=localStorage.getItem('check_out_phone_no')
        if(check_out_phone_no){
          $('#phone_no').val(check_out_phone_no);
          localStorage.removeItem('check_out_phone_no');
        }
        var check_out_email=localStorage.getItem('check_out_email')
        if(check_out_email){
          $('#email').val(check_out_email);
          localStorage.removeItem('check_out_email');
        }
        var check_out_full_name=localStorage.getItem('check_out_full_name')
        if(check_out_full_name){
          $('#full_name').val(check_out_full_name);
          localStorage.removeItem('check_out_full_name');
        }
        var check_out_company=localStorage.getItem('check_out_company')
        if(check_out_company){
          $('#company').val(check_out_company);
          localStorage.removeItem('check_out_company');
        }
        var check_out_street=localStorage.getItem('check_out_street')
        if(check_out_street){
          $('#street').val(check_out_street);
          localStorage.removeItem('check_out_street');
        }
        var check_out_floor=localStorage.getItem('check_out_floor')
        if(check_out_floor){
          $('#floor').val(check_out_floor);
          localStorage.removeItem('check_out_floor');
        }
        var check_out_door_code=localStorage.getItem('check_out_door_code')
        if(check_out_door_code){
          $('#door_code').val(check_out_door_code);
          localStorage.removeItem('check_out_door_code');
        }
        var check_out_city=localStorage.getItem('check_out_city')
        if(check_out_city){
          $('#city').val(check_out_city);
          localStorage.removeItem('check_out_city');
        } 
        var check_out_delivery_time=localStorage.getItem('check_out_delivery_time')
        if(check_out_delivery_time){
          $('#delivery_time').val(check_out_delivery_time);
          localStorage.removeItem('check_out_delivery_time');
        } 
        var check_out_paymentMethod=localStorage.getItem('check_out_paymentMethod')
        if(check_out_paymentMethod){
           $(document).find('.paymentMethod[value="'+check_out_paymentMethod+'"]').click(); 
           localStorage.removeItem('check_out_paymentMethod');
        } 
        
    }
}

/***************************************************************************************
************************Type Number only
****************************************************************************************/
$(document).on('keydown','.text-only',function(e){

    if (e.ctrlKey || e.altKey) {
      // e.preventDefault();
   } else {
       var key = e.keyCode;
       if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
           e.preventDefault();
       }
   }
});
/***************************************************************************************
************************Check Full Name
****************************************************************************************/
$(document).on('keyup','#full_name',function(){
    if($('#guestCheckoutForm').length){
        setTimeout(function() {
            $(document).find('#full_name-error').remove();        
            $(document).find('#full_name-error-1').remove();
            //var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
            var name = document.getElementById('full_name').value;
            var checkname=name.trim();
            checkname=checkname.split(" ");        
            if(checkname.length<2){                 
                $('#full_name').after('<div id="full_name-error-1" class="error">Vorname & Nachname eingeben</div>')
            }        
        }, 15);
    }
})

