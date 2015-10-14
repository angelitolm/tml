// JavaScript Document
$(document).ready(function() {
	jQuery.fn.spectragram.accessData = {
		accessToken: '578585593.a873e0a.44dd9e3e25c44965b710fd0bed0a6126', //put your access Token Here
		clientID: 'a873e0ad04744dc4ac2be1ddcf1e26f1' // put your Client ID here
	};	
	$('.vc_instagram .vc_carousel').spectragram('getUserFeed',{
		query: 'venmond', // Your instagram username
		size: 'big', // 'small' 'medium' 'big'
		max: 6, // Maximum photo, default: 10
		wrapEachWith: '<div class="vc_carousel-column"></div>'
	});	
});