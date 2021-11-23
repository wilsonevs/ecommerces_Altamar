<?php
namespace Cm;
use stdClass;


require_once dirname(__FILE__)."/phpmailer/class.phpmailer.php";

class Mail {

	public static function send(stdClass $p){
		static::sendMail($p);
	}

	public static function sendMail($p){
		$p=(object) $p;
		$p->smtp=(object) $p->smtp;

		$p->embedded_images = !empty($p->embedded_images) ? $p->embedded_images:array();

		if( !filter_var($p->from,FILTER_VALIDATE_EMAIL) ){
			throw new PublicException("Invalid sender");
		}

		if( empty($p->to) ){
			throw new PublicException("Empty recipients");
		}

		$p->reply_to = !empty($p->reply_to) ? $p->reply_to : $p->from;
		$p->reply_to = !empty($p->reply_to) ? $p->reply_to : $p->from;
		$p->to = is_array($p->to) ? $p->to:array($p->to);


		$p->cc = isset($p->cc) ? $p->cc : array();
		$p->cc = is_array($p->cc) ? $p->cc : array( $p->cc );

		$p->bcc = isset($p->bcc) ? $p->bcc : array();
		$p->bcc = is_array($p->bcc) ? $p->bcc : array( $p->bcc );


		$p->plain = !empty($p->plain) ? $p->plain:"";

		$p->from_name = !empty($p->from_name) ? $p->from_name:$p->from;

		$smtp=new \PHPMailer(true);
		$smtp->IsSMTP();
		// $smtp->SMTPDebug = 0;

		$smtp->Host = $p->smtp->host;
		$smtp->SMTPAuth = isset($p->smtp->auth) ? $p->smtp->auth:true;
		$smtp->SMTPSecure = isset($p->smtp->secure) ? $p->smtp->secure : 'tls';
		$smtp->Port = isset($p->smtp->port) ? $p->smtp->port : 587;
		$smtp->CharSet = isset($p->smtp->charset) ? $p->smtp->charset : "UTF-8";



		/*
		cando el certificado ssl es autofirmado
		"options"=>[
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		]
		*/

		if( isset($p->smtp->options) ){
			$smtp->SMTPOptions = $p->smtp->options;
		}

		//print_r($smtp->SMTPOptions);



		$smtp->Username = $p->smtp->username;
		$smtp->Password = $p->smtp->password;


		$smtp->SetFrom($p->from,$p->from_name);
		$smtp->AddReplyTo( $p->from, $p->from_name);
		//$smtp->AddCustomHeader("Reply-to: {$p->reply_to}");
		$smtp->AddCustomHeader("Return-Path: {$p->reply_to}");


		$smtp->Subject = $p->subject;



		foreach($p->embedded_images as $k=>$v){
			$smtp->AddEmbeddedImage($v, $k);
		}

		$smtp->AltBody = $p->plain;
		$smtp->MsgHTML( $p->html );




		$encoding = "base64";
		//$type = "image/jpeg";

		if (!empty($p->stringAttachments)) {
			foreach ($p->stringAttachments as $stringAttach) {
				$smtp->AddStringAttachment($stringAttach["string"], $stringAttach["name"], $encoding); //, $type);
			}
		}



		if (!empty($p->fileAttachments)) {
			foreach ($p->fileAttachments as $fileAttach) {
				if( empty($fileAttach) ) continue;
				//$path = empty($fileAttach["path"]) ? CMTempPath() : $fileAttach["path"] ;
				//AddAttachment($path,$name,$encoding,$type);
				$smtp->AddAttachment( $fileAttach["path"], $fileAttach["name"] );
			}
		}

		foreach($p->to as $recipient){
			if( empty($recipient) ) continue;

			$smtp->AddAddress($recipient);
		}

		foreach($p->cc as $recipient){
			if( empty($recipient) ) continue;

			$smtp->AddCC($recipient);
		}


		foreach($p->bcc as $recipient){
			if( empty($recipient) ) continue;

			$smtp->AddBCC($recipient);
		}

		$smtp->Send();
	}
}


/*
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
$mail->IsSMTP(); // telling the class to use SMTP

try {
  $mail->Host       = "mail.yourdomain.com"; // SMTP server
  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->Host       = "mail.yourdomain.com"; // sets the SMTP server
  $mail->Port       = 26;                    // set the SMTP port for the GMAIL server
  $mail->Username   = "yourname@yourdomain"; // SMTP account username
  $mail->Password   = "yourpassword";        // SMTP account password
  $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->AddAddress('whoto@otherdomain.com', 'John Doe');
  $mail->SetFrom('name@yourdomain.com', 'First Last');
  $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML(file_get_contents('contents.html'));
  $mail->AddAttachment('images/phpmailer.gif');      // attachment
  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
  $mail->Send();
  echo "Message Sent OK</p>\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
*/
?>
