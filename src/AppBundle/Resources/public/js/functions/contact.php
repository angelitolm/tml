<?php

require_once('phpmailer/class.phpmailer.php');

foreach($_POST as $key => $value) {
	if(ini_get('magic_quotes_gpc'))
		$_POST[$key] = stripslashes($_POST[$key]);
	
	$_POST[$key] = htmlspecialchars(strip_tags($_POST[$key]));
}

//recipient data
$toemail = $_POST['admin-email']; // Your Email Address
$toname = $_POST['admin-name']; // Your Name

//sender data
$name = $_POST['contact-form-name'];
$email = $_POST['contact-form-email'];
$service = $_POST['contact-form-service'];
$subject = $_POST['contact-form-subject'];
$message = $_POST['contact-form-message'];

$mail = new PHPMailer();


if( isset( $_POST['contact-form-submit'] ) ) {
    
    if( $name != '' AND $email != '' AND $subject != '' AND $message != '' ) {
   
		$body = "Name: $name <br><br>Email: $email <br><br>Service: $service <br><br>Message: $message";
             
		$mail->SetFrom( $email , $name );    
		$mail->AddReplyTo( $email , $name );            
		$mail->AddAddress( $toemail , $toname );            
		$mail->Subject = $subject;            
		$mail->MsgHTML( $body );
            
		$sendEmail = $mail->Send();
		
		if( $sendEmail == true ):
		    $arrResult = array ('response'=>'success');
		else:
		    $arrResult = array ('response'=>'error','message'=>$mail->ErrorInfo);		
		endif;
    } else {
		$arrResult = array ('response'=>'empty');	         
    }
    
} else {
		$arrResult = array ('response'=>'unexpected');
}
echo json_encode($arrResult);
?>