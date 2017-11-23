// JavaScript Document
// JavaScript Document
$(document).ready(function() { 
	$('#map-small').gMap({
		
		 address: 'Los Angeles, United States',
		 maptype: 'ROADMAP',
		 zoom: 14,
		 markers: [
			{
				address: "Los Angeles, United States",
				html: '<div style="width: 300px; padding:10px;"><h3 style="padding-bottom: 5px;"  class="vc_main-color">Our Headquarter</h3><p>Our mission is to help people to <strong>earn</strong> and to <strong>learn</strong> online. We operate <strong>marketplaces</strong> where hundreds of thousands of people buy and sell digital goods every day, and a network of educational blogs where millions learn <strong>creative skills</strong>.<br/></p></div>',
				icon: {
					image: "img/blue.png",
					iconsize: [42, 51],
					iconanchor: [21,51]
				}							
			}
		 ],
		 doubleclickzoom: false,
		 controls: {
			 panControl: false,
			 zoomControl: false,
			 mapTypeControl: false,
			 scaleControl: false,
			 streetViewControl: false,
			 overviewMapControl: false
		 },            
	});
});