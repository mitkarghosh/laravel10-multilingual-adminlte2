$(function () { 
	$('body').removeClass('clicked');
	lazy(); 
	/*-------------------------------------STICKY_NAV--GO_TO_TOP-------------------------*/
	if ($('.header_main .nav_wrapper').length) {
		var stickyNavTop = $('.header_main .nav_wrapper').offset().top,
            stickyNav = function () {
                var scrollTop = $(window).scrollTop();

                if (scrollTop > stickyNavTop)
                    $('body').addClass('sticky');
                else
                    $('body').removeClass('sticky');
            };
        stickyNav();

        $(window).scroll(function () {
            stickyNav();
        });
	}
 
	$(document).on('click','#mobile_cart_checkout',function(){
		$('.mainFooter').addClass('hide');	
		setTimeout(() => {
			$('.mainFooter').removeClass('hide');	
		}, 1000);
	})

	function loadScrollmobileCheckout(){
		if($(document).find('#mobile_cart_checkout').length){
			if ($(window).scrollTop() >= $('#checkout_cart').offset().top + $('#checkout_cart').outerHeight() - window.innerHeight) {
				$(document).find('#mobile_cart_checkout').addClass('hidebtnscroll');		
				//$('.mainFooter').addClass('hide');		
			}else{
				$(document).find('#mobile_cart_checkout').removeClass('hidebtnscroll');
				//$('.mainFooter').removeClass('hide');
			}
	    }
	}
	window.addEventListener("load", function(){
		setTimeout(() => {
			loadScrollmobileCheckout();
		}, 1000);
	})
	$(window).scroll(function () {
		loadScrollmobileCheckout();
		if ($(this).scrollTop() > 200)
			$('.scrollup').fadeIn();
		else
			$('.scrollup').fadeOut();
	});
	$('.scrollup').click(function (event) {
        event.preventDefault();
		$("html, body").animate({ scrollTop: 0 }, 300);
		return false;
	});

	/*-------------------------------------SUB_MENU--------------------------------------*/
	$('.nav_menu > ul > li').each(function () {
		var subMenu = $(this).children('.sub-menu');

		if(subMenu.length){
			var subWidth 		= subMenu.outerWidth(),
				subOffset 		= subMenu.offset().left,
				subTotalWidth	= subWidth + subOffset,
				containerWidth	= $(this).parents('.container').outerWidth();
			if(subTotalWidth > containerWidth) {
				subMenu.addClass('rightMenu');
			}

			$(this).children('.sub-navItem').click(function () {
				$(this).parent().siblings().removeClass('open-menu');
				$(this).parent().addClass('open-menu');


				if($(this).parent().hasClass('tt_language')) {
					$(this).siblings('.sub-menu').children('li').each(function () {
						$(this).children().on('click', function (){
							var lang = $(this).attr('data-lang');
							$(this).parent().siblings().removeClass('active');
							$(this).parent().addClass('active');
							$(this).parents('.sub-menu').siblings('.sub-navItem').html(lang + ' <i class="icon-arrow-down subarrow"></i>');
						});
					});
				}
			});
		}
	});
	/*-------------------------------------RESPONSIVE_NAV--------------------------------*/
	var nav = $(".header_main .nav_menu").html();
	var nav = $(".header_main .container").html();
	$(".responsive_nav").append(nav);
	if($(".responsive_nav").find('.nav_menu').children('ul').length) {
		$(".responsive_nav").addClass('mCustomScrollbar');
		$('.mCustomScrollbar').mCustomScrollbar({scrollbarPosition: "outside"});
	}
    
    $(document).on('click', '.header_main .responsive_btn', function () {
        $('html').addClass('responsive');
    });
    $(document).on('click', '.responsive_nav .responsive_btn', function () {
        if ($('html.responsive').length)
            $('html').removeClass('responsive');
    });
    $('.bodyOverlay').click(function () {
        if ($('html.responsive').length)
            $('html').removeClass('responsive');
    });
    
    $(document).on('click', '.subarrow', function () {
        $(this).parent().siblings().find('.sub-menu').slideUp();
        $(this).parent().siblings().removeClass('opened');

        $(this).siblings('.sub-menu').slideToggle();
        $(this).parent().toggleClass('opened');
	});
	
	/*-------------------------------------TABLE_WRAP------------------------------------*/
    $('table').each(function () {
        if (!$(this).parent().hasClass('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }
    }); 

	/*-------------------------------------CUSTOM_SCROLLBAR------------------------------*/
	$('.mCustomScrollbar').mCustomScrollbar({scrollbarPosition: "outside"});

	/*-------------------------------------STICKY_SIDEBAR--------------------------------*/
	$('.stickySidebar, .stickyContent').theiaStickySidebar({additionalMarginTop: 80});
	

	/*-------------------------------------MODAL_POPUP-----------------------------------*/
	$(document).on('click', '[data-toggle="tt_modal"]', function(event){
		event.preventDefault();
		var target = $(this).attr('data-target');
		$(target).addClass('tt_modal_show');
		$('body').addClass('tt_modal_open');
	});
	$(document).on('click', '[data-dismiss="tt_modal"]', function(event){
		event.preventDefault();
		$(this).parents('.tt_modal').removeClass('tt_modal_show');
		$('body').removeClass('tt_modal_open');
	});
	/* $(document).on('click', '.tt_modal', function(event){
		event.preventDefault();
		if(event.target.className != 'tt_modal_container'){
			$(this).removeClass('tt_modal_show');
			$('body').removeClass('tt_modal_open');
		}
	}); */

	/*-------------------------------------CATEGORY_SCROLL-------------------------------*/
	$("a").on('click', function (event) {
		if ($(this).attr('href') == '#') {
			event.preventDefault();
		}
		if (this.hash !== "") {
			event.preventDefault();

			var hash 	= this.hash;
			if($(this).hasClass('goto_cart_btn') == true)
				var hashTop	= $(hash).offset().top - 400;
			else
				var hashTop	= $(hash).offset().top - 80;

			$('html, body').animate({scrollTop: hashTop}, 800);
			
            if($(this).parents('.side_list').length) {
                $(this).parent('li').siblings().removeClass('active');
                $(this).parent('li').addClass('active');
            }
			/*$('html, body').animate({scrollTop: hashTop}, 800, function () {window.location.hash = hash;});*/
		}
	});

	/*-------------------------------------PROGRES_BAR-----------------------------------*/
	var $queue = $({});
	$(document).bind('scroll', function(event) {
		$('.progressBar').each(function(){
			var $section 		= $(this),
				scrollOffset 	= $(document).scrollTop(),
				containerOffset = $section.offset().top - window.innerHeight;

			if (scrollOffset > containerOffset) {
				var $el 		= $(this).find('.progressInner'),
					origWidth 	= $el.attr('data-percent') + '%';
				$el.width(0);
				/* $queue.queue(function(next) {
					$el.animate({width: origWidth}, 300, next);
				}); */
				$el.animate({width: origWidth}, 300);
				
				$(document).unbind('scroll');
			}
		});
	});

	/*-------------------------------------DIV_SELECT------------------------------------*/
	$(".div_select > span").on('click', function (event) {
		event.preventDefault();
		$(this).parent().toggleClass('opened');
		$(this).siblings('.price_list').slideToggle();
	});

	/*-------------------------------------MENU_ITEM-------------------------------------*/
	$(".item_group .item_arrow").bind("click", function () {
        if ($(this).parents('.item_group').hasClass('opened')) {
            $(this).parents('.item_group').siblings().removeClass('opened');
            $(this).parents('.item_group').siblings().children(".item_details").slideUp(300);
            $(this).parents('.item_group').removeClass('opened');
            /* $(this).siblings('.item_details').slideUp(300); */
            return false;
        } else {
            $(this).parents('.item_group').siblings().removeClass('opened');
            $(this).parents('.item_group').siblings().children(".item_details").slideUp(300);
            $(this).parents('.item_group').addClass('opened');
            /* $(this).siblings('.item_details').slideDown(300); */
            return false;
        }
    });

	/*-------------------------------------FORM_ITEM-------------------------------------*/
	/* $('.labelWrap input, .labelWrap textarea, .labelWrap select').each(function () {
        $(this).parent().addClass('active');
    }); */
    /* $('.labelWrap input, .labelWrap textarea, .labelWrap select').each(function () {
        if ($(this).val() != "") {
            $(this).parent().addClass('active');
        }
        if ($.trim($(this).val()) == "") {
            $(this).parent().removeClass('active');
            $(this).val("");
        }
    });
    $('.labelWrap input, .labelWrap textarea, .labelWrap select').bind('focus', function () {
        if ($.trim($(this).val()) == "") {
            $(this).parent().addClass('active');
        }
    });
    $('.labelWrap').bind('mouseup', function () {
        $(this).children('input, textarea, select').focus();
        if ($(this).children('input, textarea, select').val() == "") {
            $(this).addClass('active');
        }
    });
    $('.labelWrap input, .labelWrap textarea, .labelWrap select').blur(function () {
        if ($.trim($(this).val()) == "") {
            $(this).parent().removeClass('active');
            $(this).val("");
        }
    }); */
    /* $('.browse').change(function() {
        var filename = $('.browse').val();
        $('.browse_text').html(filename);
        $(this).parents('.labelWrap').find('.input_b').addClass('browse_b');
    });
    $('button[type="reset"]').click(function(){
        $('.browse_text').html('');
        $('.input_b').removeClass('browse_b');
    }); */

    /*-------------------------------------SHOW_PASSWORD---------------------------------*/
    $('.showPassIcon').each(function(){
        $(this).on('click', function () {
            if ($(this).hasClass('showed')) {
                $(this).siblings('input').attr('type', 'password');
                $(this).removeClass('showed');
            } else {
                $(this).siblings('input').attr('type', 'text');
                $(this).addClass('showed');
            }
        });
    });

	/*-------------------------------------DATEPICKER------------------------------------*/
    if($('[type="date"]').length) {
		if ( $('[type="date"]').prop('type') != 'date' ) {
			$('[type="date"]').datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yy-mm-dd",
				yearRange: "c-0:c+10"
			});
		}
	}
	$('.datepicker').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy",
		minDate: 0,
		/* yearRange: "c-0:c+10" */
	});

	$('.dob_datepicker').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy",
		yearRange: "-100:+0", // last hundred years
		maxDate: '+0D',
	});

	/*-------------------------------------CHANGE_AVATAR---------------------------------*/
	$('.avatar_list > ul li').each(function(){
		$(this).children('.select_avatar').click(function(){
			var id = $(this).attr('data-id');

			$(this).parent().siblings().removeClass('selected');
			$(this).parent().addClass('selected');

			$(this).parents('.tt_modal').removeClass('tt_modal_show');
			$(this).parents('body').removeClass('tt_modal_open');
		});
	});

	/*-------------------------------------CUSTOM_ALIAS----------------------------------*/
	if($('.customAlias').length && $('[name="customAlias"]').length) {
		$('[name="addressAlias"]').on('change', function(){
			if($(this).hasClass('customAlias') == true) {
				$('[name="customAlias"]').prop("disabled", false);
				$('[name="customAlias"]').focus();
			}
			else
				$('[name="customAlias"]').prop("disabled", true);
		});
		if ($('.customAlias').prop("checked")) {
			$('[name="customAlias"]').prop("disabled", false);
		}
	}

	/*-------------------------------------GIVE_RATING-----------------------------------*/
	$('.give_rating').each(function(){
		$(this).find('.rating i').on('click', function(){
			var rate_val = $(this).attr('id');
			$(this).prevAll().addClass('rated');
			$(this).addClass('rated');
			$(this).nextAll().removeClass('rated');
			$(this).parents('.give_rating').find('input').val(rate_val);
		});
	});

	/*-------------------------------------CART_SCROLL-----------------------------------*/
	if($('#cart_block').length) {
		/* var mainCartTop = $('#cart_block').offset().top,
			cartTop = function () {
				var scrollTop = $(window).scrollTop();

				if (scrollTop >= (mainCartTop - 100))
					$('.mainBody').addClass('withoutCartValue');
				else
					$('.mainBody').removeClass('withoutCartValue');
			};
		cartTop();

		$(window).scroll(function () {
			cartTop();
		}); */
	}
	else
		$('.mainBody').removeClass('withCartValue');

	/*-------------------------------------LIGHTBOX--------------------------------------*/
	lightbox.option({
		'resizeDuration': 200,
		'wrapAround': true
	});

	/*-------------------------------------QTY_ADD---------------------------------------*/
	if($('.tt_qty').length) {
		var qtyGet = 0, qtySum = 0;
		$('.tt_qtyAdd').on('click', function(){
			if($(this).hasClass('updateCartItem') == false){
				qtyGet = parseInt($(this).parent('.tt_qty').children('.tt_qtyInput').val());
				if(isNaN(qtyGet)){
					$(this).parent('.tt_qty').children('.tt_qtyInput').val(1);
					return false;
				}
				qtySum = qtyGet + 1;
				$(this).parent('.tt_qty').children('.tt_qtyInput').val(qtySum);
			}
		});
		$('.tt_qtyMinus').on('click', function(){
			if($(this).hasClass('updateCartItem') == false){
				qtyGet = parseInt($(this).parent('.tt_qty').children('.tt_qtyInput').val());
				if(isNaN(qtyGet)){
					$(this).parent('.tt_qty').children('.tt_qtyInput').val(0);
					return false;
				}
				qtySum = qtyGet - 1;
				if(qtySum < 1){
					$(this).parent('.tt_qty').children('.tt_qtyInput').val(1);
					return false;
				}
				$(this).parent('.tt_qty').children('.tt_qtyInput').val(qtySum);
			}
		});
		$('.tt_qtyInput').on('keyup', function(){
			if($(this).parent('.tt_qty').find('.updateCartItem')) {
				if (this.value.match(/[^0-9]/g)) {
					this.value = this.value.replace(/[^0-9]/g, '');
					if (($(this).val() == "") || ($(this).val() == 0)) {
						$(this).val('1');
					}
				}
			}
		});
		$('.tt_qtyInput').on('blur', function(){
			if($(this).parent('.tt_qty').find('.updateCartItem')) {
				if (($(this).val() == "") || ($(this).val() == 0)) {
					$(this).val('1');
				}
			}
		});
	}

	/*-------------------------------------HTML_CLICK------------------------------------*/
	$(document).on('click', 'html', function (event) {
		event.stopPropagation();
		if($('.nav_menu > ul > li').length){
			if(event.target.className != 'sub-navItem')
				$('.nav_menu > ul > li').removeClass('open-menu');
		}
	});
});

function lazy(){
    $(".lazy").lazyload({effect: 'fadeIn', delay: 1000});
}


/**
 * Credit Card VAlidation
 */

$(document).on('keyup','#stripePaymentForm input',function(){

	      if($(this).val()){
			  $(this).closest('div').find('.stripe_card_error').remove();
		  }  
		$(document).find('.stripe_card_error').remove();
		if($('#stripe_cardnumber').val()==''){
			if(siteLang=='de'){
			$('#stripe_cardnumber').after('<div class="error cm-0 cp-0 stripe_card_error">Bitte überprüfen Sie die Kartennummer.</div>');
			}else{
				$('#stripe_cardnumber').after('<div class="error cm-0 cp-0 stripe_card_error">Please enter valid card number</div>');
			}
		} 
		if($('#stripe_expirationdate').val()==''){
			if(siteLang=='de'){
			$('#stripe_expirationdate').after('<div class="cm-0 cp-0 error stripe_card_error">Bitte überprüfen Sie das Verfallsdatum.</div>');
			}else{
				$('#stripe_expirationdate').after('<div class="cm-0 cp-0 error stripe_card_error">Please enter valid expiry date</div>');
			}
		} 
		if($('#stripe_securitycode').val()==''){
			if(siteLang=='de'){
			$('#stripe_securitycode').after('<div class="cm-0 cp-0 error stripe_card_error">Bitte überprüfen Sie den CVC Code.</div>');
			}else{
				$('#stripe_securitycode').after('<div class="cm-0 cp-0 error stripe_card_error">Please enter cvc number</div>');
			}
		} 
})
 
$(document).on('submit','#stripePaymentForm',function(event){

	    event.preventDefault()

        var siteLang    = $('#website_lang').val();
		$(document).find('.stripe_card_error').remove();
		if($('#stripe_cardnumber').val()==''){
			if(siteLang=='de'){
			  $('#stripe_cardnumber').after('<div class="error cm-0 cp-0 stripe_card_error">Bitte überprüfen Sie die Kartennummer.</div>');
			}else{
				$('#stripe_cardnumber').after('<div class="error cm-0 cp-0 stripe_card_error">Please enter valid card number</div>');
			}
		} 
		if($('#stripe_expirationdate').val()==''){
			if(siteLang=='de'){
			  $('#stripe_expirationdate').after('<div class="cm-0 cp-0 error stripe_card_error">Bitte überprüfen Sie das Verfallsdatum.</div>');
			}else{
				$('#stripe_expirationdate').after('<div class="cm-0 cp-0 error stripe_card_error">Please enter valid expiry date</div>');
			}
		} 
		if($('#stripe_securitycode').val()==''){
			if(siteLang=='de'){
			  $('#stripe_securitycode').after('<div class="cm-0 cp-0 error stripe_card_error">Bitte überprüfen Sie den CVC Code.</div>');
			}else{
				$('#stripe_securitycode').after('<div class="cm-0 cp-0 error stripe_card_error">Please enter cvc number</div>');
			}
		} 

       if($(document).find('.stripe_card_error').length<1){
			$('body').addClass('clicked');
			// if (ajax_check) {
			// 	return;
			// }
			// ajax_check = true;
			var site_url = $("#website_link").val();
			var siteLang    = $('#website_lang').val();
			var formData = new FormData(this);
			var cancelOrderUrl = site_url + '/'+siteLang+'/payment-process-stripe';
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			//console.log(formData);
			$.ajax({
				url: cancelOrderUrl,
				method: 'POST',
				data: formData,
				dataType: "json",
				processData: false, 
				contentType: false,
				success: function (response) {
					//ajax_check = false;
					$('body').removeClass('clicked');
					if(response.errorget==0){
						var errormsg='<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close stripe-error-close" type="button">×</button><span>'+response.error+'</span><br/></div>';
						$(document).find('.stripe-payment-error').html(errormsg);
						location.href=site_url+ '/'+siteLang+'/thank-you/'+response.oid;
					}else{
						var errormsg='<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close stripe-error-close" type="button">×</button><span>'+response.error+'</span><br/></div>';
						$(document).find('.stripe-payment-error').html(errormsg);
					}
				}
			});
	 }

})

/**
 * Stripe error close
 */
$(document).on('click','.stripe-error-close',function(){{
	$(document).find('.stripe-payment-error').html('');
}})

$(document).on('keyup','#stripe_expirationdate',function(){

	    var thisvals=$(this).val();
		// if (thisvals.length === 2){
		// 		thisvals = thisvals + '/'
		// 		$(this).val(thisvals);
		// }
		// else{    
		// 	if (thisvals.length === 3 && thisvals.charAt(2) === '/'){
		// 		//ele.value = ele.value.replace('/', '');
		// 		$(this).val(thisvals.replace('/', ''));
		// 	}else{
		// 		 if(thisvals.length>5){
		// 			$(this).val(thisvals.substring(0,5));
		// 		 }
		// 	} 
		// }
		newvalue=thisvals.replace('/', '');
		if(newvalue.length>2){
			$(this).val(newvalue.substring(0,2)+'/'+newvalue.substring(2,4));
		} 
})

$(document).on('keypress keyup blur','.allownumericwithoutdecimal',function(event){
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
})


 /**
 * offer model popup
 */
 function bannerOffer() {
	let en_banner_adv = localStorage.getItem('en_banner_adv');
	let de_banner_adv = localStorage.getItem('de_banner_adv');
	let enbanner = $('.offer-en-banner').val();
	let debanner = $('.offer-de-banner').val();
	if (en_banner_adv != enbanner || de_banner_adv != debanner) {
		$('#offer-popup').addClass('tt_modal_show');
		$('body').addClass('tt_modal_open');
		localStorage.removeItem('en_banner_adv');
		localStorage.removeItem('de_banner_adv');
		$(document).on('click', '.save-close-banner', function () {
			if ($('#dont-show-avd').prop('checked') == true) {
				localStorage.setItem('en_banner_adv', enbanner);
				localStorage.setItem('de_banner_adv', debanner);
			}  
			$('#offer-popup').removeClass('tt_modal_show');
			$('body').removeClass('tt_modal_open');
		})
	}
}
bannerOffer();