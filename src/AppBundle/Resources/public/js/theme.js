/*****************************************************************
Table of Contents

1.) Document Ready State
	- Google Search
	- Nice Scroll
	- Back Top
	- Tool Tips
	- Pop Over
	- Pretty Photo
	- Sticky Menu
	- Active Menu
	- Newsletter Subscribe
	- Twitter Feeds
	- Primary Menu Responsive Button
	
2.) Window Load State
	- Latest Portfolio Carousel Widget
	- Client Carousel Widget
	- Testimonial Carousel Widget
	- Sidebar Portfolio Carousel Widget
	- Single Portfolio Carousel Widget
	- Related Project Carousel Widget
	- Twitter Carousel Widget
	

!Note: You can search using the title above
*****************************************************************/

/* Document Ready State. 
   Used: Global */	
jQuery(document).ready(function($)
	{

		/* Google Search */
		$('.vc_menu-search-text').keyup(function (e) {  
			$('.gsc-input').val($(this).val());
			if (e.keyCode == 13) {
				$('td.gsc-search-button .gsc-search-button').click();
			}
		});
		$('.vc_menu-search-submit').click(function(e){
			$('td.gsc-search-button .gsc-search-button').click();
		});
		
		/* Back Top. 
		   Used: Buttton at the corner right to scroll to the top */		
		$('#back-top').click(function(e){
				e.preventDefault();
				$('body,html').animate({scrollTop:0},800);
		});		
		
		/* Tool Tips. 
		   Used: <a class="tooltip-trigger"> */
		$('a[class^="tooltip-trigger"]').tooltip();	
		
		/* Pop Over. 
		   Used: <a class="popover-trigger"> */	
		$('a[class^="popover-trigger"]').popover();
		
		/* Pretty Photo. 
		   Used: - For Grouping:  <a data-rel="prettyPhoto[portfolio-group]"> 
		         - For Single Image: <a data-rel="prettyPhoto"> 
		*/
		$('a[data-rel]').each(function() {
			$(this).attr('rel', $(this).data('rel'));
		});
		$("a[rel^='prettyPhoto']").prettyPhoto({theme:'light_square'});		
		

		/* Sticky Menu. 
		   Used: Global */	
		var headerHeight = $("header").height();
//		var headerMarginBottom = parseInt($("header").css("margin-bottom"));
//		var paddingHeight = headerHeight  + headerMarginBottom;
//		alert(headerHeight);
		var marginHeader = 0;
		if ($('header').hasClass('header-1') || $('header').hasClass('header-4')){
			marginHeader = 8;
		}
		var logo = $("header .logo img");
		var logoSmallHeight = 60;
		var submenuHeight = $("header .vc_secondary-menu").height();
		var pageWidthLimit = 975;

		function checkStickyMenu(){
			if($("body").hasClass("boxed")) return false;
		
			if($(window).scrollTop() > headerHeight-submenuHeight   &&  $(window).width() >= pageWidthLimit){
				// #Back-Top visible
				$('#back-top').addClass('visible');
				if($("body").hasClass("sticky-menu-active"))
					return false;
				$("body").addClass("sticky-menu-active");
		
				$("body").css('padding-top',headerHeight + marginHeader);		
				$('header').css({
							top: -headerHeight+40,
							opacity:'.8',
						}).stop(true, true).animate({
							top: 0,
							opacity: '1'
					}, 1000, function(){
						$('header').removeAttr('style');
						// Animation complete.
					});
				logo.height(logoSmallHeight).css("width", "auto");				
			} else if( $(window).scrollTop() == 0 ||  $(window).width() < pageWidthLimit){
				$('#back-top').removeClass('visible');
				if ($("body").hasClass("sticky-menu-active")){
					$("body").css('padding-top',0);
					$("body").removeClass("sticky-menu-active");
				}
				logo.removeAttr('style');
			}
		}
		$(window).on("scroll", function(){
				checkStickyMenu();
		});
		$(window).on("resize", function(){
				checkStickyMenu();
				if ($(window).width()>pageWidthLimit){ /* Remove Collapse On width > pageWidthLimit */	
					$(".mega-menu-drop-down").css('display','none');		
					$('.vc_primary-menu').removeClass('in collapse').removeAttr('style');
					$('.vc_primary-menu .vc_mega-menu').removeAttr('style');
					$('.vc_primary-menu > ul').removeAttr('style');
				}					
		});
		checkStickyMenu();


		// Active Menu
		var active_page = $('header').attr('data-active');
		$('header .vc_primary-menu > ul > li#'+active_page).addClass('active');


		/* Newsletter Subscribe. 
		   Used: At the footer fourth column */
		$("#newsletter").validate({
					submitHandler: function(form) {
						var action = $(this).attr('action');
						$(form).ajaxSubmit({
							type: "POST",
							url: $("#newsletter-form").attr("action"),
							data: {
								"email": $("#newsletter-form #email").val()
							},
							dataType: "json",
							success: function (data) {
								if (data.response == "success") {
		
									$("#vc_newsletter-form-success").removeClass("hidden");
									$("#vc_newsletter-form-error").addClass("hidden");
		
									$("#newsletter-form #email")
										.val("")
										.blur()
										.closest(".control-group")
										.removeClass("success")
										.removeClass("error");
									$("#vc_newsletter-form-widget .info").fadeOut(500,function(){
										$('#vc_newsletter-form-success').fadeIn(500);
									});	
		
								} else {
									$("#vc_newsletter-form-error").html(data.message);
									$("#vc_newsletter-form-error").removeClass("hidden");
									$("#vc_newsletter-form-success").addClass("hidden");
		
									$("#newsletter-form #email")
										.blur()
										.closest(".control-group")
										.removeClass("success")
										.addClass("error");
									
								}						
							}
						});	
					},		
					success: function(data){
						$(data).closest(".control-group").removeClass("error").addClass("success");
					},
					error: function() {
						$('.vc_newsletter-form-success').html('Sorry, an error occurred.').css('color', 'red');
					}		 		
		}); 
		
		/* Twitter Feeds. 
		   Used: Footer, Widget  */
		$("#twitter-feeds").tweet({
			modpath: '/functions/twitter/',
			username: "envato",
			join_text: "auto",
			count: 8,
			auto_join_text_default: "we said,", 
			auto_join_text_ed: "we",
			auto_join_text_ing: "we were",
			auto_join_text_reply: "we replied to",
			auto_join_text_url: "we were checking out",
			loading_text: "loading tweets..."
		}); 
		$('#twitter-feeds .tweet_list').addClass('vc_li vc_carousel');	
			
		$("#twitter-feeds-mini").tweet({
			modpath: '/functions/twitter/',
			username: "envato",
			join_text: "auto",
			count: 4,
			auto_join_text_default: "we said,", 
			auto_join_text_ed: "we",
			auto_join_text_ing: "we were",
			auto_join_text_reply: "we replied to",
			auto_join_text_url: "we were checking out",
			loading_text: "loading tweets..."
		}); 	
		$('#twitter-feeds-mini .tweet_list').addClass('vc_carousel');	
		$('#twitter-feeds-mini .tweet_list li').addClass('vc_carousel-column');							
		/* Primary Menu Responsive Button
		   used: header
		*/			
		$("header .vc_menu div.vc_primary-menu > ul .vc_mega-menu").prev()
			.append('<i class="icon-chevron-sign-down mega-menu-drop-down" style="display:none; "></i>')
			.attr('href','javascript:void(0);')
			.click(function(){
				if( $(window).width() < pageWidthLimit){
					$(this).next().toggle();	
				}
		});
		
		$(".btn-navbar").click(function () {
			$("header .vc_menu div.vc_primary-menu > ul").css('height','480px');
			$(".mega-menu-drop-down").toggle();	
		});		
		
		
});

/* Window Load State. 
   Used: Global 
   Info: Basically we gather up all carousels in this state
   */	
$(window).load(function()
	{		
		/* Latest Portfolio Carousel Widget.  
		   Used: index.html, index-portfolio.html, index-revolution.html */
		$(".vc_latest-portfolio .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
				}
			},
			next:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
				}
			},
			width: '100%',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 2000
			},
			scroll:{
				items: 1,
				duration: 400,
				wipe: true
			},
			items:{
				visible:{
					min: 1,
					max: 3
				},
				width: 480,
				height: 'auto'
			}
		});
		
		/* Latest Portfolio Carousel Widget.  
		   Used: index.html, index-portfolio.html, index-revolution.html */
		$(".vc_instagram .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
				}
			},
			next:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
				}
			},
			width: '100%',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 2000
			},
			scroll:{
				items: 1,
				duration: 400,
				wipe: true
			},
			items:{
				visible:{
					min: 1,
					max: 3
				},
				width: 480,
				height: 'auto'
			}
		});
		
		/* Client Carousel Widget.  
		   Used: index.html, index-portfolio.html, index-revolution.html, about.html */
		$(".vc_client .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function(){
					return $(this).parent().parent().parent().children('.met_carousel_control').children('a:first-child')
				}
			},
			next:{
				button : function(){
					return $(this).parent().parent().parent().children('.met_carousel_control').children('a:last-child')
				}
			},
			width: 'auto',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 1000
			},
			items:{
				visible:{
					min: 1,
					max: 6
				},
				height: 152
			},
			pagination  : ".vc_client .vc_pager"
		});

		/* Testimonial Carousel Widget.  
		   Used: about.html */
		testimonial();   
		function testimonial(){   
		   $(".vc_testimonial .vc_carousel").carouFredSel({
				direction: "down",
				height: 'variable',
				width: 'variable',
				items: {
						visible: 1,
						width: 'auto'	
				},
				auto: 5000,
				circular: false,
				infinite: true,
				pagination  : ".vc_testimonial-pager"				
			});
		}

		/* Sidebar Portfolio Carousel Widget.  
		   Used: Every page with sidebar */		
		$(".vc_portfolio-widget .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function()
				{
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
				}
			},
			next:{
				button : function()
				{
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
				}
			},
			width: '100%',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 2000
			},
			items:{
				visible:{
					min: 1,
					max: 2
				},
				width:480,
				height: 'auto'
			}
		});
		
		/* Single Portfolio Carousel Widget.  
		   Used: portfolio-single-project-full-width.html, portfolio-single-project.html */				
		$(".vc_single-portfolio .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
				}
			},
			next:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
				}
			},
			width: '100%',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 2000
			},
			items:{
				visible:{
					max: 1
				},
				width:570,
				height: 'auto'
			}
		});	
		
		/* Related Project Carousel Widget.  
		   Used: portfolio-single-project-full-width.html, portfolio-single-project.html, blog-single.html */
		$(".vc_related-project .vc_carousel").carouFredSel({
			responsive: true,
			prev:{
				button : function()
				{
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
				}
			},
			next:{
				button : function(){
					return $(this).parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
				}
			},
			width: '100%',
			circular: false,
			infinite: true,
			auto:{
				play : true,
				pauseDuration: 0,
				duration: 2000
			},
			scroll:{
				items: 4,
				duration: 400,
				wipe: true
			},
			items:{
				visible:{
					min: 1,
					max: 4
				},
				width: 277,
				height: 'auto'
			}
		});		

		/* Twitter Carousel Widget. 
		   Used: footer 3rd Column  */			   
		update_twitter();
		$(window).resize(function(){
			resize_twitter();
			testimonial();
		});			   	
		function update_twitter(){
			$(".vc_twitter .vc_carousel").carouFredSel({
				direction: "down",
				height: 'variable',
				width: '100%',
				items: {
						visible: 3,
						width: 'auto'	
				},
				auto: 4000,
				circular: false,
				infinite: true,
				prev:{
					button : function(){
						return $(this).parent().parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
					}
				},
				next:{
					button : function(){
						return $(this).parent().parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
					}
				}
			});	
			$(".vc_twitter .vc_carousel, .vc_twitter .caroufredsel_wrapper").css("width","100%");
			$(".vc_twitter .vc_carousel").trigger("updateSizes");	
			
			$(".vc_twitter-mini .vc_carousel").carouFredSel({
				width: 'auto',
				responsive:'true',
				items: {
					max: 1
				},
				auto: 6000,
				circular: true,
				infinite: true,
				prev:{
					button : function(){
						return $(this).parent().parent().parent().parent().children('.vc_carousel-control').children('a:first-child')
					}
				},
				next:{
					button : function(){
						return $(this).parent().parent().parent().parent().children('.vc_carousel-control').children('a:last-child')
					}
				}
			});									
		}	
		function resize_twitter(){
			$(".vc_twitter .vc_carousel, .vc_twitter .caroufredsel_wrapper").css("width","100%");
			$(".vc_twitter .vc_carousel").trigger("updateSizes");							
		}

									
});