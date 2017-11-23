// JavaScript Document
$(document).ready(function() {
	var options = { 
		type: "POST",
		url:  $("#contact-form").attr('action'),
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
			$("#contact-form").find('#contact-form-submit #spin').remove();
			$("#contact-form").find('#contact-form-submit').removeClass('disabled').removeAttr('disabled').blur();	 

			$("#contact-form").fadeOut(500, function(){
				$('#contact-form-result').fadeIn(500);
			});										
		},
		error: function() {
				$("#contact-form-result div").addClass('hidden');
				$("#contact-form-result #unexpected").removeClass('hidden');	
		}
	}; 	
	
	$("#contact-form").validate({
		submitHandler: function(form) {
			
			$(form).find('#contact-form-submit').prepend('<i id="spin" class="icon-spinner icon-spin"></i>').addClass('disabled').attr('disabled');
			
			$(form).ajaxSubmit(options);
			
		},
		success: function(form){	
		}
	});
});