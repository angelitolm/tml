// JavaScript Document
/* Metro Slider.  
   Used: index.html, index-portolio.html */
   
/* Metro Slider Variable Setting */
var iFrequency = 5000; // slide movement period expressed in miliseconds
var movement = 2; // how many movement/transition
var pauseOnHover = 1; // 1 = Pause on Hover, 0 = Not Pause on Hover   


var iDo=0;
var myInterval;
var target =  $(".vc_metro-wrapper");
var run=false;


$(window).load(function(){	  
	metro_slider();
	if (!run) {
		if (($('.vc_metro-wrapper:hover').length != 0) & (pauseOnHover)) {
    		stopLoop();
		} else {
			startLoop();
		}
		 run=true;  
	}
	target.hover(function() { 
			if (pauseOnHover & run) { stopLoop();}
		},function() {  	
			if (pauseOnHover & run) { startLoop();}
	});		
});

$(window).resize(function(){
	metro_slider(); /*Do Metro Slider */
});

function metro_slider(){
	var metroSlider = 0;
	$('.vc_metro-slider ul li').each(function(){
			var slide_width= $(this).width();
			if ($(this).hasClass('odd')){
				metroSlider = metroSlider + slide_width + 5;

			} else if ($(this).hasClass('even')){
				metroSlider = metroSlider + slide_width +5 ;
			}
	});	
	metroSlider+=5;
	$('.vc_metro-slider').css('width', metroSlider + 'px');
	var padding=70;
	var scrollwidth=$('.container').width()-padding;
			
	$('html.no-touch .vc_metro-wrapper').tinyscrollbar({
		axis: 'x',
		size: scrollwidth,
		sizethumb: 'auto',
		invertscroll: true
	});
	$('html.touch .vc_metro-wrapper .viewport').css({
		'width': $(window).innerWidth()+'px',
		'overflow-x' :'scroll',
	});	
	$('html.touch .vc_metro-wrapper .scrollbar').hide();
}

function startLoop(){
	myInterval = setInterval( runSlide, iFrequency );  // run
}
function stopLoop(){
	clearInterval(myInterval);
}
			   
function runSlide(){
	var pos= $('.vc_metro-wrapper .thumb').position();
	var barpos = pos.left;

	var pos= $('.vc_metro-wrapper .overview').position();
	var slidepos = pos.left;

	//increment calculation
	var barinc= ($('.vc_metro-wrapper .track').width() - $('.vc_metro-wrapper .thumb').width()) / movement;
	
	var widthconst = $(window).width();
	if ($('body').hasClass('boxed')) { widthconst = $('.container').width()+50; }
	
	var slideinc= (widthconst - $('.vc_metro-wrapper .vc_metro-slider').width()) / movement;

	// make 0 again when reach max
	iDo = (iDo+ 1) % (movement+1);

	//animate the metroslider
	$('html.no-touch .vc_metro-wrapper .thumb').animate({
			left: iDo * barinc
		}, 1000, function(){
	});
	$('html.no-touch .vc_metro-wrapper .overview').animate({
			left: iDo * slideinc -iDo*2
		}, 1000, function(){
	});
	
	$('html.touch .vc_metro-wrapper .viewport').animate({
			scrollLeft: iDo * (($('.vc_metro-wrapper .vc_metro-slider').width() - $(window).innerWidth()) / movement )
		}, 1000, function(){
	});		
		
/*	$('html.touch .vc_metro-wrapper .viewport').scrollLeft(iDo * slideinc -iDo*2);/*animate({
			$(this).scrollLeft(iDo * slideinc -iDo*2);
		}, 1000, function(){
	});	*/
}