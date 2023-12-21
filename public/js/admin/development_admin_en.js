var site_url = $("#website_admin_link").val();
var ajax_check = false;

$.validator.addMethod("valid_email", function(value, element) {
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid email");

//Phone number eg. (+91)9876543210
$.validator.addMethod("valid_number", function(value, element) {
    if (/^(?=.*[0-9])[- +()0-9]+$/.test(value)) {
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

//Positive number
$.validator.addMethod("valid_positive_number", function(value, element) {
    if (/^[0-9]+$/.test(value)) {
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

//Integer (0) and decimal
$.validator.addMethod("valid_min_amount", function(value, element) {
    if (/^[0-9]\d*(\.\d+)?$/.test(value)) {
        return true;
    } else {
        return false;
    }
});

//Youtube url checking
$.validator.addMethod("valid_youtube_url", function(value, element) {
    if (/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/.test(value)) {
        return true;
    } else {
        return false;
    }
});


$(document).ready(function() {
    var websiteLink = $('#website_link').val();
    var siteLang    = $('#website_lang').val();
    
    $('.admin_website_language').on('click', function() {
        var langValue = $(this).data('lang');
        
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


    /* Admin Login Form */
    $("#adminLoginForm").validate({
        rules: {
            email: {
                required: true,
                valid_email: true
            },
            password: {
                required: true,
            },
        },
        messages: {
            email: {
                required: "Please enter email",
            },
            password: {
                required: "Please enter new password",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    // Start :: Forgot password Form //
    $("#adminForgotPasswordForm").validate({
        rules: {
            email: {
                required: true,
                valid_email: true
            }
        },
        messages: {
            email: {
                required: "Please enter email",
                valid_email: "Please enter valid email"
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            $('#loading').show();
            form.submit();
        }
    });
    // End :: Forgot password Form //

    // Start :: Reset password Form //
    $("#adminResetPasswordForm").validate({
        rules: {
            password: {
                required: true,
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            },
        },
        messages: {
            password: {
                required: "Please enter password",
            },
            confirm_password: {
                required: "Please enter confirm password",
                equalTo: "Password should be same",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            $('#loading').show();
            form.submit();
        }
    });
    // End :: Reset password Form //

    /* Admin Profile Update */
    $("#updateAdminProfile").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                valid_email: true,
            },
            phone_no: {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "Please enter first name"
            },
            last_name: {
                required: "Please enter last name"
            },
            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter phone number",
            },
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Admin Password Update */
    $("#updateAdminPassword").validate({
        rules: {
            current_password: {
                required: true,
            },
            password: {
                required: true,
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            current_password: {
                required: "Please enter current password",
            },
            password: {
                required: "Please enter new password",
            },
            confirm_password: {
                required: "Please enter confirm password",
                equalTo: "Password should be same as new password",
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* User Profile Add*/
    $("#addUserProfile").validate({
        rules: {
            full_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            email: {
                required: true,
                required: '#phone_no:blank',
                valid_email: function() {
                    if ($("#email").val() != '') {
                        return true;
                    }
                }
            },
            phone_no: {
                required: true,
                required: '#email:blank',
                valid_number: function() {
                    if ($("#phone_no").val() != '') {
                        return true;
                    }
                }
            },
            password: {
                required: true,
                valid_password: true,
            },
            confirm_password: {
                required: true,
                valid_password: true,
                equalTo: "#password"
            },
            user_type: {
                required: true
            },
            back_role_id: {
                required: true
            },
            "front_role_id[]": {
                required: true
            }
        },
        messages: {
            full_name: {
                required: "Please enter name",
                minlength: "Name should be at least 2 characters",
                maxlength: "Name must not be more than 255 characters"
            },

            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter phone number",
            },
            password: {
                required: "Please enter new password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            confirm_password: {
                required: "Please enter confirm password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter",
                equalTo: "Password should be same as new password",
            },
            back_role_id: {
                required: "Please select any role"
            },
            "front_role_id[]": {
                required: "Please select any role"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('type') == 'checkbox') {
                error.insertAfter($(element).parents('div.form-control'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* User Profile Add*/
    $("#addAdminUserProfile").validate({
        rules: {
            full_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            email: {
                required: true,
                valid_email: true
            },
            phone_no: {
                required: true,
                valid_number: true
            },
            password: {
                required: true,
                valid_password: true,
            },
        },
        messages: {
            full_name: {
                required: "Please enter name",
                minlength: "Name should be at least 2 characters",
                maxlength: "Name must not be more than 255 characters"
            },
            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter phone number",
            },
            password: {
                required: "Please enter new password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* User Profile Update*/
    $("#updateAdminUserProfile").validate({
        rules: {
            full_name: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            email: {
                required: true,
                valid_email: true
            },
            phone_no: {
                required: true,
                valid_number: true
            },
            password: {
                valid_password: function() {
                    if ($("#password").val() != '') {
                        return true;
                    }
                }
            },
        },
        messages: {
            full_name: {
                required: "Please enter full name",
                minlength: "Full name should be at least 2 characters",
                maxlength: "Full name must not be more than 255 characters"
            },
            last_name: {
                required: "Please enter last name",
                minlength: "Last name should be at least 2 characters",
                maxlength: "Last name must not be more than 255 characters"
            },
            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter phone number",
            },
            password: {
                required: "Please enter new password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* User Password Update */
    $("#updateUserPassword").validate({
        rules: {
            password: {
                required: true,
                valid_password: true,
            },
            confirm_password: {
                required: true,
                valid_password: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please enter new password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter"
            },
            confirm_password: {
                required: "Please enter confirm password",
                valid_password: "Min. 8, alphanumeric, special character & a capital letter",
                equalTo: "Password should be same as new password",
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Category start */
    $("#addCategoryForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editCategoryForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Category end */

    /* Drink start */
    $("#addDrinkForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                // required: function() {
                //     CKEDITOR.instances.description_en.updateElement();
                // },
                required: true,
                minlength: 10
            },
            description_de: {
                // required: function() {
                //     CKEDITOR.instances.description_de.updateElement();
                // },
                required: true,
                minlength: 10
            },
            price: {
                required: true,
                valid_amount: true
            },
            image: {
                required: true, 
                // accept: "jpg|png|jpeg|svg", 
                // filesize: 5048576
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
            image: {
                required: "Please select image",
                // accept:   "File must be as jpg, jpeg, png or svg",
                // filesize: "File size not more then 5mb"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#updateDrinkForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                // required: function() {
                //     CKEDITOR.instances.description_en.updateElement();
                // },
                required: true,
                minlength: 10
            },
            description_de: {
                // required: function() {
                //     CKEDITOR.instances.description_de.updateElement();
                // },
                required: true,
                minlength: 10
            },
            price: {
                required: true,
                valid_amount: true
            },
            image: {
                // required: true, 
                // accept: "jpg|png|jpeg|svg", 
                // filesize: 5048576
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
            image: {
                // required: "Please select image",
                // accept:   "File must be as jpg, jpeg, png or svg",
                // filesize: "File size not more then 5mb"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Drink End */

    /* Tag start */
    $("#addTagForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            image: {
                required: true,
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            image: {
                required: "Please select image",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editTagForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Tag End */

    /* Ingredient start */
    $("#addIngredientForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            price: {
                required: true,
                valid_amount: true
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#updateIngredientForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            price: {
                required: true,
                valid_amount: true
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Ingredient End */
    
    /* Allergen start */
    $("#addAllergenForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            image: {
                required: true,
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            image: {
                required: "Please select image",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editAllergenForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Allergen End */

    /* Product Start */
    $("#addProductForm").validate({
    	ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            // description_en: {
            //     required: function() {
            //         CKEDITOR.instances.description_en.updateElement();
            //     },
            //     minlength: 10
            // },
            // description_de: {
            //     required: function() {
            //         CKEDITOR.instances.description_de.updateElement();
            //     },
            //     minlength: 10
            // },
            category_id: {
                required: true
            },
            // image: {
            //     required: true
            // },
            // price: {
            //     required: true,
            //     valid_amount: true
            // },
            // 'tags[]': {
            //     required: true,
            // },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            // description_en: {
            //     required: "Please enter description",
            //     minlength: "Please enter 10 characters"
            // },
            // description_de: {
            //     required: "Please enter description",
            //     minlength: "Please enter 10 characters"
            // },
            category_id: {
                required: "Please select category",
            },
            // image: {
            //     required: "Please select image",
            // },
            // price: {
            //     required: "Please enter price",
            //     minlength: "Please enter valid price"
            // },
            // 'tags[]': {
            //     required: "Please select tag(s)",
            // },
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2') && element.next('.select2-container').length) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editProductForm").validate({
    	ignore: [],
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            // description_en: {
            //     required: function() {
            //         CKEDITOR.instances.description_en.updateElement();
            //     },
            //     minlength: 10
            // },
            // description_de: {
            //     required: function() {
            //         CKEDITOR.instances.description_de.updateElement();
            //     },
            //     minlength: 10
            // },
            category_id: {
                required: true
            },
            // image: {
            //     required: true
            // },
            // price: {
            //     required: true,
            //     valid_amount: true
            // },
            // 'tags[]': {
            //     required: true,
            // },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter 10 characters"
            },
            category_id: {
                required: "Please select category",
            },
            // image: {
            //     required: "Please select image",
            // },
            // price: {
            //     required: "Please enter price",
            //     minlength: "Please enter valid price"
            // },
            'tags[]': {
                required: "Please select tag(s)",
            },
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2') && element.next('.select2-container').length) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
  	});  
    /* Product end */

    /* Avatar start */
    $("#addAvatarForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            image: {
                required: true,
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            image: {
                required: "Please select image",
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editAvatarForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Avatar End */

     /* Pin Code start */
     $("#addAddonProduct").validate({       
        errorPlacement: function(error, element) {
            if ($(element).attr('id') == 'minimum_order_amount') {
                error.insertAfter($(element).parents('div#minimum_order_amount_div'));
            } else if ($(element).attr('id') == 'delivery_charge') {
                error.insertAfter($(element).parents('div#delivery_charge_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Pin Code start */
    $("#addPinCodeForm").validate({
        rules: {
            code: {
                required: true,
            },
            area: {
                required: true,
            },
            minimum_order_amount: {
                required: true,
                valid_min_amount: true
            },
            // delivery_charge: {
            //     required: true,
            //     valid_min_amount: true
            // },
        },
        messages: {
            code: {
                required: "Please enter pin code",
            },
            area: {
                required: "Please enter area",
            },            
            minimum_order_amount: {
                required: "Please enter minimum order amount",
                valid_min_amount: "Please enter valid amount"
            },
            // delivery_charge: {
            //     required: "Please enter delivery charge",
            //     valid_min_amount: "Please enter valid amount"
            // },
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('id') == 'minimum_order_amount') {
                error.insertAfter($(element).parents('div#minimum_order_amount_div'));
            } else if ($(element).attr('id') == 'delivery_charge') {
                error.insertAfter($(element).parents('div#delivery_charge_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editPinCodeForm").validate({
        rules: {
            code: {
                required: true,
            },
            area: {
                required: true,
            },
            minimum_order_amount: {
                required: true,
                valid_min_amount: true
            },
            // delivery_charge: {
            //     required: true,
            //     valid_min_amount: true
            // },
        },
        messages: {
            code: {
                required: "Please enter pin code",
            },
            area: {
                required: "Please enter area",
            },            
            minimum_order_amount: {
                required: "Please enter minimum order amount",
                valid_min_amount: "Please enter valid amount"
            },
            // delivery_charge: {
            //     required: "Please enter delivery charge",
            //     valid_min_amount: "Please enter valid amount"
            // },
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('id') == 'minimum_order_amount') {
                error.insertAfter($(element).parents('div#minimum_order_amount_div'));
            } else if ($(element).attr('id') == 'delivery_charge') {
                error.insertAfter($(element).parents('div#delivery_charge_div'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Pin Code End */

    /* Special Menu start */
    $("#addSpecialMenuForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                // required: function() {
                //     CKEDITOR.instances.description_en.updateElement();
                // },
                required: true,
                minlength: 10
            },
            description_de: {
                // required: function() {
                //     CKEDITOR.instances.description_de.updateElement();
                // },
                required: true,
                minlength: 10
            },
            price: {
                required: true,
                valid_amount: true
            },
            image: {
                required: true, 
                // accept: "jpg|png|jpeg|svg", 
                // filesize: 5048576
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
            image: {
                required: "Please select image",
                // accept:   "File must be as jpg, jpeg, png or svg",
                // filesize: "File size not more then 5mb"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#updateSpecialMenuForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                // required: function() {
                //     CKEDITOR.instances.description_en.updateElement();
                // },
                required: true,
                minlength: 10
            },
            description_de: {
                // required: function() {
                //     CKEDITOR.instances.description_de.updateElement();
                // },
                required: true,
                minlength: 10
            },
            price: {
                required: true,
                valid_amount: true
            },
            image: {
                // required: true, 
                // accept: "jpg|png|jpeg|svg", 
                // filesize: 5048576
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            price: {
                required: "Please enter price",
                minlength: "Please enter valid price"
            },
            image: {
                // required: "Please select image",
                // accept:   "File must be as jpg, jpeg, png or svg",
                // filesize: "File size not more then 5mb"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Special Menu End */

    /* Delivery Area start */
    $("#addDeliveryAreaForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#updateDliveryAreaForm").validate({
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Delivery Area End */

    /* Faq start */
    $("#addFaqForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                required: function() {
                    CKEDITOR.instances.description_en.updateElement();
                },
                minlength: 10
            },
            description_de: {
                required: function() {
                    CKEDITOR.instances.description_de.updateElement();
                },
                minlength: 10
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#updateFaqForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                required: function() {
                    CKEDITOR.instances.description_en.updateElement();
                },
                minlength: 10
            },
            description_de: {
                required: function() {
                    CKEDITOR.instances.description_de.updateElement();
                },
                minlength: 10
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Faq End */

    /* Help start */
    $("#addHelpForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                required: function() {
                    CKEDITOR.instances.description_en.updateElement();
                },
                minlength: 10
            },
            description_de: {
                required: function() {
                    CKEDITOR.instances.description_de.updateElement();
                },
                minlength: 10
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editHelpForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            description_en: {
                required: function() {
                    CKEDITOR.instances.description_en.updateElement();
                },
                minlength: 10
            },
            description_de: {
                required: function() {
                    CKEDITOR.instances.description_de.updateElement();
                },
                minlength: 10
            },
        },
        messages: {
            title: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter title",
                minlength: "Title should be at least 2 characters",
                maxlength: "Title must not be more than 255 characters"
            },
            description_en: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
            description_de: {
                required: "Please enter description",
                minlength: "Please enter minimum 10 characters"
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Help End */
    

    /* Site Settings */
    $("#updateSiteSettingsForm").validate({
        rules: {
            from_email: {
                required: true,
                valid_email: true
            },
            to_email: {
                required: true,
                valid_email: true
            },
            order_email: {
                required: true,
                valid_email: true
            },
            website_title: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            website_link: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            // minimum_order_amount: {
            //     required: true,
            //     valid_amount: true
            // },
            footer_address: {
                required: true,
            },
            min_delivery_delay_display: {
                required: true,
            },
        },
        messages: {
            from_email: {
                required: "Please enter from email",
            },
            to_email: {
                required: "Please enter to email",
            },
            order_email: {
                required: "Please enter order email",
            },
            website_title: {
                required: "Please enter website title",
                minlength: "Website title should be at least 2 characters",
                maxlength: "Website title must not be more than 255 characters"
            },
            website_link: {
                required: "Please enter website link",
                minlength: "Website link should be at least 2 characters",
                maxlength: "Website link must not be more than 255 characters"
            },
            // minimum_order_amount: {
            //     required: "Please enter minimum order amount",
            //     valid_amount: "Please enter valid amount"
            // },
            footer_address: {
                required: "Please enter footer address",
            },
            min_delivery_delay_display: {
                required: "Please enter minimum delivery delay time",
            }
        },
        errorPlacement: function(error, element) {
            // if ($(element).attr('id') == 'minimum_order_amount') {
            //     error.insertAfter($(element).parents('div#minimum_order_amount_div'));
            // } else {
                error.insertAfter(element);
            // }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Edit Cms*/
    $("#updateCmsForm").validate({
        ignore: [],
        debug: false,
        rules: {
            title_en: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            title_de: {
                required: true,
                minlength: 2,
                maxlength: 255
            },
            // description: {
            //     required: function() {
            //         CKEDITOR.instances.description.updateElement();
            //     },
            //     minlength: 10
            // },
            // description2: {
            //     required: function() {
            //         CKEDITOR.instances.description2.updateElement();
            //     },
            //     minlength: 10
            // },
            meta_title: {
                required: true,
            },
            meta_keyword: {
                required: true,
            },
            meta_description: {
                required: true,
            },
        },
        messages: {
            title_en: {
                required: "Please enter page title",
                minlength: "Title (English) name should be at least 2 characters",
                maxlength: "Title (English) name must not be more than 255 characters"
            },
            title_de: {
                required: "Please enter page title",
                minlength: "Title (Dutch) name should be at least 2 characters",
                maxlength: "Title (Dutch) name must not be more than 255 characters"
            },
            // description: {
            //     required: "Please enter description"
            // },
            // description2: {
            //     required: "Please enter description 2"
            // },
            meta_title: {
                required: "Please enter meta title"
            },
            meta_keyword: {
                required: "Please enter meta keyword"
            },
            meta_description: {
                required: "Please enter meta description"
            },
        },
        submitHandler: function(form) {
            form.submit();
        }
    });    

    /* Role Form Start */
    $("#createRoleForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                maxlength: 255
            }
        },
        messages: {
            name: {
                required: "Please enter role name",
                minlength: "Role name should be at least 2 characters",
                maxlength: "Role name must not be more than 255 characters"
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editRoleForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2,
                maxlength: 255
            }
        },
        messages: {
            name: {
                required: "Please enter role name",
                minlength: "Role name should be at least 2 characters",
                maxlength: "Role name must not be more than 255 characters"
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Role Form Start */
    
    /* Sub admin Form Start */
    $("#addSubAdminForm").validate({
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
                valid_email: true,
            },
            phone_no: {
                required: true,
            },
            'role[]': {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "Please enter first name",
                minlength: "First name should be at least 2 characters",
                maxlength: "First name must not be more than 255 characters"
            },
            last_name: {
                required: "Please enter last name",
                minlength: "Last name should be at least 2 characters",
                maxlength: "Last name must not be more than 255 characters"
            },
            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter contact number",
            },
            'role[]': {
                required: "Please select role",
            },
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2') && element.next('.select2-container').length) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $("#editSubAdminForm").validate({
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
                valid_email: true,
            },
            phone_no: {
                required: true,
            },
            'role[]': {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "Please enter first name",
                minlength: "First name should be at least 2 characters",
                maxlength: "First name must not be more than 255 characters"
            },
            last_name: {
                required: "Please enter last name",
                minlength: "Last name should be at least 2 characters",
                maxlength: "Last name must not be more than 255 characters"
            },
            email: {
                required: "Please enter email",
            },
            phone_no: {
                required: "Please enter contact number",
            },
            'role[]': {
                required: "Please select role",
            },
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('select2') && element.next('.select2-container').length) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    /* Role Form Start */

    /* Add Coupon */
    $("#addCouponForm").validate({
        ignore: [],
        debug: false,
        rules: {
            code: {
                required: true,
                valid_coupon_code: true
            },
            has_minimum_cart_amount: {
                required: true
            },
            discount_type: {
                required: true
            },
            amount: {
                required: true,
                valid_amount: true
            },
            start_time: {
                required: true
            }
        },
        messages: {
            code: {
                required: "Please enter coupon code",
                valid_coupon_code: "Coupon code consist only alphabets and numbers",
            },
            has_minimum_cart_amount: {
                required: "Please select minimum cart type",
            },
            discount_type: {
                required: "Please select discount type",
            },
            amount: {
                required: "Please enter amount",
                valid_amount: "Please enter valid amount",
            },
            start_time: {
                required: "Please select start date & time",
            }
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Edit Coupon */
    $("#editCouponForm").validate({
        ignore: [],
        debug: false,
        rules: {
            code: {
                required: true,
                valid_coupon_code: true
            },
            has_minimum_cart_amount: {
                required: true
            },
            discount_type: {
                required: true
            },
            amount: {
                required: true,
                valid_amount: true
            },
            start_time: {
                required: true
            }
        },
        messages: {
            code: {
                required: "Please enter coupon code",
                valid_coupon_code: "Coupon code consist only alphabets and numbers",
            },
            has_minimum_cart_amount: {
                required: "Please select minimum cart type",
            },
            discount_type: {
                required: "Please select discount type",
            },
            amount: {
                required: "Please enter amount",
                valid_amount: "Please enter valid amount",
            },
            start_time: {
                required: "Please select start date & time",
            }
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Add Special Hour */
    $("#addSpecialHourForm").validate({
        ignore: [],
        debug: false,
        rules: {
            special_date: {
                required: true,
            },
        },
        messages: {
            special_date: {
                required: "Please select date",
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    /* Edit Special Hour */
    $("#editSpecialHourForm").validate({
        ignore: [],
        debug: false,
        rules: {
            special_date: {
                required: true,
            },
        },
        messages: {
            special_date: {
                required: "Please select date",
            },
        },
        errorPlacement: function(error, element) {
            if(element.parent().hasClass('input-group')){
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    
    /*Date range used in Admin user listing (filter) section*/
    //Restriction on key & right click
    $('#registered_date').keydown(function(e) {
        var keyCode = e.which;
        if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 8 || keyCode === 122 || keyCode === 32 || keyCode == 46) {
            e.preventDefault();
        }
    });
    $('#registered_date').daterangepicker({
        autoUpdateInput: false,
        timePicker: false,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        startDate: moment().startOf('hour'),
        //endDate: moment().startOf('hour').add(24, 'hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function(start_date, end_date) {
        $(this.element[0]).val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));
    });

    $('#purchase_date').daterangepicker({
        autoUpdateInput: false,
        timePicker: false,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        startDate: moment().startOf('hour'),
        //endDate: moment().startOf('hour').add(24, 'hour'),
        locale: {
            // format: 'YYYY-MM-DD'
            format: 'DD.MM.YYYY'
        },
        dateLimit: {
            'months': 3,
        }
    }, function(start_date, end_date) {
        // $(this.element[0]).val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));
        $(this.element[0]).val(start_date.format('DD.MM.YYYY') + ' - ' + end_date.format('DD.MM.YYYY'));
    });

    $('#contract_duration').daterangepicker({
        autoUpdateInput: false,
        timePicker: false,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        startDate: moment().startOf('hour'),
        //endDate: moment().startOf('hour').add(24, 'hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function(start_date, end_date) {
        $(this.element[0]).val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));
    });

    /*Date range used in Coupon listing (filter) section*/
    //Restriction on key & right click
    $('.date_restriction').keydown(function(e) {
        var keyCode = e.which;
        if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 8 || keyCode === 122 || keyCode === 32 || keyCode == 46) {
            e.preventDefault();
        }
    });
    $('.date_restriction').daterangepicker({
        autoUpdateInput: false,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        startDate: moment().startOf('hour'),
        //endDate: moment().startOf('hour').add(24, 'hour'),
        locale: {
            format: 'YYYY-MM-DD HH:mm'
        }
    }, function(start_date, end_date) {
        $(this.element[0]).val(start_date.format('YYYY-MM-DD HH:mm') + ' - ' + end_date.format('YYYY-MM-DD HH:mm'));
    });

    $("#settlement_status").select2();
});

function sweetalertMessageRender(target, message, type, confirm = false) {
    let options = {
        icon: type,
        title: 'warning!',
        text: message,        
    };
    if (confirm) {
        options['showCancelButton'] = true;
        options['confirmButtonText'] = 'Yes';
    }
    return Swal.fire(options)
    .then((result) => {
        if (confirm == true && result.value) {
            window.location.href = target.getAttribute('data-href'); 
        } else {
            return (false);
        }
    });
}

$("#purchase_date").keypress(function(event) {event.preventDefault();});
$("#contract_duration").keypress(function(event) {event.preventDefault();});

$(".view_shipment_details").click(function () {
	var shipmentid = $(this).data('shipmentid');	
	if (ajax_check) {
		return;
	}

	ajax_check = true;
	var viewShipmentDetailsUrl = site_url + '/securepanel/contract/view-shipment-details';
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		url: viewShipmentDetailsUrl,
		method: 'post',
		data: {
			shipmentid: shipmentid
		},
		success: function (response) {
			ajax_check = false;
			$('#shipment_detail_view').html(response);
			$('#shipmentModal').modal('show');
		}
	});
});

// Live Processing orders to set Delivered
function processingToDeliveredSweetalertMessageRender(target, message, type, confirm = false) {
    var rowId       = target.getAttribute('data-rowid');
    var siteLang    = $('#website_lang').val();
    let options = {
        icon: type,
        title: 'warning!',
        text: message,        
    };
    if (confirm) {
        options['showCancelButton'] = true;
        options['confirmButtonText'] = 'Yes';
    }
    return Swal.fire(options)
    .then((result) => {
        if (confirm == true && result.value) {
            if (ajax_check) {
                return;
            }
            ajax_check = true;
            var statusUpdateUrl = site_url + '/securepanel/'+siteLang+'/orders/processing-status';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: statusUpdateUrl,
                method: 'POST',
                data: {
                    rowId: rowId
                },
                success: function (response) {
                    ajax_check = false;
                    getList();
                }
            });
        } else {
            return (false);
        }
    });
}

// Cancel orders
function cancelOrderSweetAlertMessageRender(target, message, type, confirm = false) {
    var rowId       = target.getAttribute('data-rowid');
    var siteLang    = $('#website_lang').val();
    let options = {
        icon: type,
        title: 'warning!',
        text: message,        
    };
    if (confirm) {
        options['showCancelButton'] = true;
        options['confirmButtonText'] = 'Yes';
    }
    return Swal.fire(options)
    .then((result) => {
        if (confirm == true && result.value) {
            if (ajax_check) {
                return;
            }
            ajax_check = true;
            var cancelOrderUrl = site_url + '/securepanel/'+siteLang+'/orders/cancel-order';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: cancelOrderUrl,
                method: 'POST',
                data: {
                    rowId: rowId
                },
                success: function (response) {
                    ajax_check = false;
                    getList();
                }
            });
        } else {
            return (false);
        }
    });
}


//sp2
$(document).on('click','.add-addon-submit',function(){
   // alert();
    var attr=error='';
    var formvalue=[];
    $(document).find('.errorfeild').remove();
    $(document).find('#addAddonProductNew').find('input').each(function(){
        console.log('iviod');
        var attr = $(this).attr('required');
        if (typeof attr !== 'undefined' && attr !== false) {
            if($(this).val().trim()==''){
                error='<label  class="error errorfeild">This field is required.</label>';
                $(this).after(error);
                formvalue.push(1);
            }
        }
    })
    if(formvalue.length<1){
        $('#addAddonProductNew').submit();
    }
})
//sp2 require field check
$(document).on('keyup','#addAddonProductNew input',function(){
      $(this).closest('.form-group').find('.errorfeild').remove();
})
/***************************************************************************************
************************Coupon Code Check
****************************************************************************************/
$(document).on('change','#discount_type',function(){
         var label='CHF';
         if($(this).val()=='P'){
             label='%';
         }
         $(document).find('.discount_type_lable_change').text(label);
})
 

$(document).on('keypress blur keyup', '.numberonly', function(event) {

    $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
    }
   
});

/**
 * Remove Logo Images
 */
$(document).on('click','.remove-image-logos',function(){
     $(this).closest('.form-group').find('input').val('');
     $(this).closest('.logo-wrap-div').hide();
     $(this).closest('.img-wrap-div').hide();
}) 
/**
 * show logo on change
 */
$(document).on('change','input[name="logo"],input[name="logo_png"],input[name="header_picture"]',function(){

        var filePath = $(this).val().toLowerCase();
        $(document).find('#from_image_custom-error').remove();
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        var validimage=1;
        if(!allowedExtensions.exec(filePath)){
            validimage=0;
            $(this).val('');
            var siteLang    = $('#website_lang').val();
            if(siteLang=='de'){
               $(this).after('<label id="from_image_custom-error" class="error" for="from_email">Bitte wählen Sie ein gültiges Bild aus.</label>');
            }else{
                $(this).after('<label id="from_image_custom-error" class="error" for="from_email">Please select a valid image.</label>');
            }
        }
        if($(this).closest('#updateSiteSettingsForm').length && validimage==1){
            $(this).closest('.form-group').find('.img-wrap-div').removeClass('hide');
            $(this).closest('.form-group').find('.logo-wrap-div').removeClass('hide');
            $(this).closest('.form-group').find('.img-wrap-div').show();
            $(this).closest('.form-group').find('.logo-wrap-div').show();
            //$(this).addClass('get-dynamic-image');
            var thiddiv=$(this).closest('.form-group');
            readImgURL(this)
            setTimeout(function() { 
                var txtmsg=$(document).find('.read-img-url-create').val();
                $(document).find('.read-img-url-create').remove();
                thiddiv.find('.img-wrap-div img').attr('src',txtmsg);
                thiddiv.find('.logo-wrap-div img').attr('src',txtmsg);
            }, 200);
            
        }
})

/**
 *Priview Image  
 **/
function readImgURL(input) {
    var imgurl='';
    if (input.files && input.files[0]) {
        var reader = new FileReader();
       reader.onload = function (e) {
            //$('#blah').attr('src', e.target.result);
            //return e.target.result;
            //input.httr('data-img-url',e.target.result);
            $('body').append("<input type='hidden' class='read-img-url-create' value='"+e.target.result+"'>");
        }
        reader.readAsDataURL(input.files[0]);
    }  
}