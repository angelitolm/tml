/* Portfolio List. 
   Used: portfolio-2-columns.php, portfolio-3-columns.php, portfolio-4-columns.php, portfolio-left-sidebar.php, portfolio-right-sidebar.php  */
$(window).load(function(){	   
	var $container = $('#portfolio');		
	$container.isotope();	
	$('#portfolio-filter a').click(function(){	
		$('#portfolio-filter li').removeClass('active');
		$(this).parent('li').addClass('active');
		var selector = $(this).attr('data-filter');							
		$container.isotope({ filter: selector });
		return false;			
	});	
	$(window).resize(function() {								
		$container.isotope('reLayout');
	});	
});