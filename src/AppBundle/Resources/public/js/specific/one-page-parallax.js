// JavaScript Document
$(document).ready(function() {
    //Cache some variables
    var links = $('.vc_primary-menu  ul li a');
    slide = $('.slide-waypoint');

	padding_top = 42;
	header_height = $('header').height();
	submenuHeight = $("header .vc_secondary-menu").height();
	calHeight = 120;	

    htmlbody = $('html,body');	
	
	
    //initialise Stellar.js
    $(window).stellar({
		horizontalScrolling: false
	});	
	
	$(window).on("resize", function(){
    	$(window).stellar('refresh');	
		$('.vc_banner .banner-icon').css('display','none');	
		$(window).on("scroll", function(){
			$('.vc_banner .banner-icon').css('display','block');	
		});
	});

	
	
	// icon animation	
	$(window).on("scroll", function(){
	   var offset = $(document).scrollTop()
		   ,opacity=0
		   , limit=200
		;			
		banner_icon = $('.vc_banner .banner-icon .icon');
		banner_icon_opacity = $('.vc_banner .banner-icon .icon').css('opacity');
		if (offset < limit-30){
			opacity=offset/limit;
			$('.vc_banner .banner-icon .icon').css('opacity',opacity);	
		}
	});



    //Setup waypoints plugin
    slide.waypoint(function(direction) {
//  		alert('Direction example triggered scrolling ' + direction);
        //cache the variable of the data-waypoint attribute associated with each slide
        dataslide = $(this).attr('data-waypoint');



        //If the user scrolls up change the navigation link that has the same data-waypoint attribute as the slide to active and 
        //remove the active class from the previous navigation link 
		if (direction === 'down') {	
			resetActive();
            $('.vc_primary-menu  ul li a[data-waypoint="' + dataslide + '"]').parent().addClass('active');		
		}
		else {
			resetActive();
            $('.vc_primary-menu  ul li a[data-waypoint="' + dataslide + '"]').parent().prev().addClass('active');						
		}
		

    },{offset:calHeight+60});


	
    //Create a function that will be passed a slide number and then will scroll to that slide using jquerys animate. The Jquery
    //easing plugin is also used, so we passed in the easing method of 'easeInOutQuint' which is available throught the plugin.
    function goToByScroll(dataslide) {
        htmlbody.animate({
           scrollTop: $('.slide-waypoint[data-waypoint="' + dataslide + '"]').offset().top -calHeight
        }, 1500, 'easeInOutQuint');
    }

	function resetActive(){
		$('.vc_primary-menu  ul li').removeClass('active');	
	}
    //When the user clicks on the navigation links, get the data-waypoint attribute value of the link and pass that variable to the goToByScroll function
	
    $('.vc_primary-menu  ul li a[data-waypoint="home"]').click(function (e) {		
        e.preventDefault();
		htmlbody.animate({scrollTop:0},1500, 'easeInOutQuint');
    });	
    $('.vc_primary-menu   ul  li  a[data-waypoint]').click(function (e) {		
        e.preventDefault();
		
        dataslide = $(this).attr('data-waypoint');
		goToByScroll(dataslide);		
    });
	
	
	// landing animation
    $(".vc_signup").waypoint(function () {
        $(".vc_signup .text-col").addClass("animated fadeInLeft");
        $(".vc_signup .newsletter-col").addClass("animated fadeInRight");		
    }, 	{ offset: 600	});
	
	
    $(".vc_features-block").waypoint(function () {
        $(".vc_features-block .first-row-col-1, .vc_features-block .first-row-col-2").addClass("animated fadeInLeft");
        $(this).delay(800).queue(function () {
        	$(".vc_features-block .second-row-col-1, .vc_features-block .second-row-col-2").addClass("animated fadeInRight");
        });			
    }, 	{ offset: 600	});	
				
				
    $(".vc_pricing-tables").waypoint(function () {
        $('.vc_pricing-tables h2').addClass("animated fadeInDown");		
        $(this).delay(600).queue(function () {
                $('.vc_pricing-tables .vc_pricing-table').addClass("animated flipInX")
        });
    }, 	{ offset: 600	});	
	
	
    $(".vc_portfolio").waypoint(function () {
        $(".vc_portfolio").addClass("animated flipInY");	
    }, 	{ offset: 600	});
		
		
    $(".vc_client").waypoint(function () {
        $('.vc_client h4').addClass("animated fadeInDown");	
        $(this).delay(300).queue(function () {
                $('.vc_client .subtitle').addClass("animated fadeInDown");				
        });	
		 $('.vc_client .subtitle').delay(900).queue(function () {
			$('.vc_client .vc_carousel-wrap').addClass("animated fadeInDown")
		});									
    }, 	{ offset: 600	});		
    $(".vc_testimonial-block").waypoint(function () {
        $('.vc_testimonial-block .block-title').addClass("animated fadeInDown");	
        $(this).delay(600).queue(function () {
                $('.vc_testimonial-block .text-1').addClass("animated fadeInLeft");	
                $('.vc_testimonial-block .text-2').addClass("animated fadeInRight");								
        });					
    }, 	{ offset: 600	});		
	
    $(".vc_map").waypoint(function () {
        $('.vc_map').addClass("animated flipInX");	
						
    }, 	{ offset: 600	});	
	
	
    $(".vc_contact-us").waypoint(function () {
        $('.vc_contact-us .text-1').addClass("animated  fadeInLeft");	
        $('.vc_contact-us .text-2').addClass("animated fadeInRight");						
    }, 	{ offset: 600	});				
});