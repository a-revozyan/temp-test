// Mean Menu
jQuery('.mean-menu').meanmenu({ 
	meanScreenWidth: "991"
});

jQuery('form').attr('autocomplete', 'off');

// Preloader
jQuery(window).on('load', function() {
    $('.preloader').fadeOut();
});


function formatNumber(num, round) {
  if(round != 0 && !round) round = 2;
  return num.toFixed(round).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ')
}

function nvl(value) {
  return value ? value : 0;
}

// Nice Select JS
$('select').niceSelect();

// Header Sticky
$(window).on('scroll', function() {
    if ($(this).scrollTop() >150){  
        $('.navbar-area').addClass("is-sticky");
    }
    else{
        $('.navbar-area').removeClass("is-sticky");
    }
});

//  Main Slider Area
$('.hero-slider-wrap').owlCarousel({
	loop:true,
	margin:0,
	nav:false,
	mouseDrag: true,
	items:1,
	dots: true,
	autoHeight: true,
	autoplay: true,
	smartSpeed:1500,
	autoplayHoverPause: true,
	navText: [
		"<i class='flaticon-back'></i>",
		"<i class='flaticon-right'></i>",
	],
});

// Partners Wrap
$('.partners-wrap').owlCarousel({ 
	loop:true,
	nav:false,
	autoplay:true,
	autoplayHoverPause: true,
	autoplayTimeout:500,
	mouseDrag: true,
	margin: 0,
	center: false,
	dots: false,
	smartSpeed:500,
	responsive:{
		0:{
			items:1,
		},
		576:{
			items:2,
		},
		768:{
			items:3,
		},
		992:{
			items:3,
		},
		1200:{
			items:3,
		}
	}
});

// Testimonial Wrap
$('.testimonial-wrap').owlCarousel({
	loop:true,
	margin:30,
	nav:false,
	mouseDrag: true,
	items:1,
	dots: false,
	autoHeight: true,
	autoplay: true,
	smartSpeed:1500,
	autoplayHoverPause: true,
	center: true,
	responsive:{
		0:{
			items:1,
		},
		576:{
			items:2,
		},
		768:{
			items:2,
		},
		992:{
			items:3,
		},
		1200:{
			items:3,
		}
	}
});

// Team Wrap
$('.team-wrap').owlCarousel({
	loop:true,
	margin:30,
	nav:false,
	mouseDrag: true,
	items:1,
	dots: true,
	autoHeight: true,
	autoplay: true,
	smartSpeed:1500,
	autoplayHoverPause: true,
	center: true,
	responsive:{
		0:{
			items:1,
		},
		576:{
			items:2,
		},
		768:{
			items:2,
		},
		992:{
			items:3,
		},
		1200:{
			items:3,
		}
	}
});

$('.partner-wrap').owlCarousel({
	loop:true,
	margin:30,
	nav:false,
	mouseDrag: true,
	items:5,
	dots: true,
	autoHeight: true,
	autoplay: true,
	smartSpeed:3000,
	autoplayTimeout:1000,
	autoplayHoverPause: true,
	center: true,
	responsive:{
      0: {
        items: 1,
        nav: false,
      },
      600: {
        items: 3,
      },
      1000: {
        items: 5,
      }
	}
});

// Testimonial Wrap Two
$('.testimonial-wrap-two').owlCarousel({
	loop:true,
	margin:30,
	nav:false,
	mouseDrag: true,
	items:1,
	dots: false,
	autoHeight: true,
	autoplay: true,
	smartSpeed:1500,
	autoplayHoverPause: true,
	responsive:{
		0:{
			items:1,
		},
		576:{
			items:1,
		},
		768:{
			items:2,
		},
		992:{
			items:3,
		},
		1200:{
			items:3,
		}
	}
});

// Work Wrap
$('.work-wrap').owlCarousel({
	loop:true,
	margin:0,
	nav:false,
	mouseDrag: true,
	items:1,
	dots: false,
	autoHeight: true,
	autoplay: true,
	smartSpeed:1500,
	autoplayHoverPause: true,
	responsive:{
		0:{
			items:1,
		},
		576:{
			items:1,
		},
		768:{
			items:2,
		},
		992:{
			items:3,
		},
		1200:{
			items:5,
		}
	}
});

// Odometer 
$('.odometer').appear(function(e) {
	var odo = $(".odometer");
	odo.each(function() {
		var countNumber = $(this).attr("data-count");
		$(this).html(countNumber);
	});
});

// Go to Top
// Scroll Event
$(window).on('scroll', function(){
	var scrolled = $(window).scrollTop();
	if (scrolled > 300) $('.go-top').addClass('active');
	if (scrolled < 300) $('.go-top').removeClass('active');
});  

// Click Event
$('.go-top').on('click', function() {
	$("html, body").animate({ scrollTop: "0" },  500);
});

$('.services').on('click', function() {
	$("html, body").animate({ scrollTop: $('.main-banner-area-two').height() - 20 },  1000);
});

$('.about').on('click', function() {
	$("html, body").animate({ scrollTop: $('.main-banner-area-two').height() + $('.our-products').height() + 100 },  1000);
});

// FAQ Accordion
$('.accordion').find('.accordion-title').on('click', function(){
	// Adds Active Class
	$(this).toggleClass('active');
	// Expand or Collapse This Panel
	$(this).next().slideToggle('fast');
	// Hide The Other Panels
	$('.accordion-content').not($(this).next()).slideUp('fast');
	// Removes Active Class From Other Titles
	$('.accordion-title').not($(this)).removeClass('active');		
});

// Count Time 
function makeTimer() {
    var endTime = new Date("march  30, 2020 17:00:00 PDT");			
    var endTime = (Date.parse(endTime)) / 1000;
    var now = new Date();
    var now = (Date.parse(now) / 1000);
    var timeLeft = endTime - now;
    var days = Math.floor(timeLeft / 86400); 
    var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
    var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
    var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));
    if (hours < "10") { hours = "0" + hours; }
    if (minutes < "10") { minutes = "0" + minutes; }
    if (seconds < "10") { seconds = "0" + seconds; }
    $("#days").html(days + "<span>Days</span>");
    $("#hours").html(hours + "<span>Hours</span>");
    $("#minutes").html(minutes + "<span>Minutes</span>");
    $("#seconds").html(seconds + "<span>Seconds</span>");
}
setInterval(function() { makeTimer(); }, 300);

// Animation
new WOW().init();

// Tabs 
$('.tab ul.tabs').addClass('active').find('> li:eq(0)').addClass('current');
$('.tab ul.tabs li').on('click', function (g) {
	var tab = $(this).closest('.tab'), 
	index = $(this).closest('li').index();
	tab.find('ul.tabs > li').removeClass('current');
	$(this).closest('li').addClass('current');
	tab.find('.tab_content').find('div.tabs_item').not('div.tabs_item:eq(' + index + ')').slideUp();
	tab.find('.tab_content').find('div.tabs_item:eq(' + index + ')').slideDown();
	g.preventDefault();
});
 
// Popup Video
$('.popup-youtube, .popup-vimeo').magnificPopup({
    disableOn: 300,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,
    fixedContentPos: false,
});

// Subscribe form
$(".newsletter-form").validator().on("submit", function (event) {
	if (event.isDefaultPrevented()) {
	// handle the invalid form...
		formErrorSub();
		submitMSGSub(false, "Please enter your email correctly.");
	} else {
		// everything looks good!
		event.preventDefault();
	}
});
function callbackFunction (resp) {
	if (resp.result === "success") {
		formSuccessSub();
	}
	else {
		formErrorSub();
	}
}
function formSuccessSub(){
	$(".newsletter-form")[0].reset();
	submitMSGSub(true, "Thank you for subscribing!");
	setTimeout(function() {
		$("#validator-newsletter").addClass('hide');
	}, 4000)
}
function formErrorSub(){
	$(".newsletter-form").addClass("animated shake");
	setTimeout(function() {
		$(".newsletter-form").removeClass("animated shake");
	}, 1000)
}
function submitMSGSub(valid, msg){
	if(valid){
		var msgClasses = "validation-success";
	} else {
		var msgClasses = "validation-danger";
	}
	$("#validator-newsletter").removeClass().addClass(msgClasses).text(msg);
}

// AJAX MailChimp
$(".newsletter-form").ajaxChimp({
	url: "https://Envy Theme.us20.list-manage.com/subscribe/post?u=60e1ffe2e8a68ce1204cd39a5&amp;id=42d6d188d9", // Your url MailChimp
	callback: callbackFunction
});

// MixItUp Shorting
$('.shorting').mixItUp();

// Search Popup JS
$('.close-btn').on('click',function() {
	$('.search-overlay').fadeOut();
	$('.search-btn').show();
	$('.close-btn').removeClass('active');
});
$('.search-btn').on('click',function() {
	$(this).hide();
	$('.search-overlay').fadeIn();
	$('.close-btn').addClass('active');
});