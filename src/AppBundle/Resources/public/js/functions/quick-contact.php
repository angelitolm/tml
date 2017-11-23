<?php

require_once('phpmailer/class.phpmailer.php');

$mail = new PHPMailer();

//recipient data
$toemail = $_POST['admin-email']; // Your Email Address
$toname = $_POST['admin-name']; // Your Name

//sender data
$name = $_POST['contact-form-name'];
$email = $_POST['contact-form-email'];
$message = $_POST['contact-form-message'];


if( isset( $_POST['contact-form-submit'] ) ) {
    
    if( $name != '' AND $email != ''  AND $message != '' ) {
   
		$body = "Name: $name <br><br>Email: $email <br><br>Message: $message";
             
		$mail->SetFrom( $email , $name );    
		$mail->AddReplyTo( $email , $name );            
		$mail->AddAddress( $toemail , $toname );            
		$mail->Subject = "Quick Message From: " . $name;            
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