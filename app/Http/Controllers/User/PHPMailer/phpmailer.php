<?php

// PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception;
// Base files 
require 'PHPMailer/src/Exception.php';
require 'assets/plugins/PHPMailer/src/PHPMailer.php';
require 'assets/plugins/PHPMailer/src/SMTP.php';

//Send Mail
	$objet = "[Support] Création de compte $username";
	$body = "
        <h3>Félicitations, votre compte est activé !</h3>
        Bonjour $username,<br/><br/>
        La création de votre compte est terminée.<br/>
        Vous pouvez vous connecter sur l'application avec les identifiants suivants :<br/><br/>
        <a href='$app_url'><b><u>$app_url</u></b></a><br/><br/>
        Login : $email<br/>
        Mot de passe : $pwd1<br/><br/>
        Merci de votre confiance.<br/>
        ";

	$mail = new PHPMailer(true);

	try {
	    
	    $data_smtp = $dataSmtp;

	    $mail->isSMTP(); // using SMTP protocol                                     
	    $mail->Host = $data_smtp['host']; // SMTP host as gmail 
	    $mail->SMTPAuth = true;  // enable smtp authentication                             
	    $mail->Username = $data_smtp['username'];  // sender gmail host              
	    $mail->Password = $data_smtp['password']; // sender gmail host 
	    $mail->SMTPSecure = $data_smtp['protocol'];  // for encrypted 
	    $mail->Port = $data_smtp['port'];   // port for SMTP     
	    $mail->setFrom($data_smtp['email']); // sender's email and name
	    
	    $mail->addAddress($email);

	    $mail->isHTML(true);
	    $mail->CharSet = "UTF-8";
	    $mail->Subject = $objet;
	    $mail->Body    = $body;

	    $mail->send();
	    $stm = true;
	} catch (Exception $e) {
	    $stm = false;
	}        

?>