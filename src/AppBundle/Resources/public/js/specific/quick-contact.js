// JavaScript Document
/* Quick Contact.  
   Used: index.php, index-portolio.php, index-revolution.php, features-widgets.php, features-mega-menu.php */
   
$(document).ready(function() {
	
	var options = { 
		type: "POST",
		url:  $("#contact-form-widget").attr('action'),
		dataType: "json",
		success: function(data) {
			if (data.response == "success") {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #success").removeClass('hidden');						
			} else if (data.response == "error") {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #error").removeClass('hidden');	
				$("#contact-form-result #error").append(data.message);				
			} else if (data.response == "empty") {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #empty").removeClass('hidden');						
			} else if (data.response == "unexpected") {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #unexpected").removeClass('hidden');						
			}	
			$("#contact-form-widget").find('#contact-form-submit #spin').remove();
			$("#contact-form-widget").find('#contact-form-submit').removeClass('disabled').removeAttr('disabled').blur();	 

			$("#contact-form-widget").fadeOut(500, function(){
				$('#contact-form-result').fadeIn(500);
			});										
		},
		error: function() {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #unexpected").removeClass('hidden');	
		}
	}; 	
	
	$("#contact-form-widget").validate({
		submitHandler: function(form) {			
			$(form).find('#contact-form-submit').prepend('<i id="spin" class="icon-spinner icon-spin"></i>').addClass('disabled').attr('disabled');			
			$(form).ajaxSubmit(options);			
		},
		success: function(form){	
		}
	});
	
	
});