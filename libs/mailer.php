<?php
/*
very basic mailer class

howto:

mailer_start();
$mailer = new KahukMailer('Welcome to Kahuk', 'Welcome to Kahuk.Com. Thanks for joining!', 'admin@kahuk.com', 'user@user.com');
$mailer->send();

mailer_start -- in utils.php, checks to see if another mailer is being used (like Swift Mailer)
  -- if it is, then include the code for that and use it
  -- if not, include this class, and use it

KahukMailer(subject, body, to, from);

Currently only supports 1 to address

Returns true if success, false if failure.

*/

if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

class KahukMailer{

	var $subject = '';
	var $body = '';
	var $from = '';
	var $to = '';

	function __construct($subj = '', $body = '', $from = '', $to = '', $cc = '', $bcc = ''){

		if($subj != ''){$this->subject = $subj;}
		if($body != ''){$this->body = $body;}
		if($from != ''){$this->from = $from;}

		if(isset($to) && !is_array($to)){$to[] = $to;}
		$this->to = $to;
			
		if(isset($cc) && $cc != '' && !is_array($cc)){$cc[] = $cc;}
		$this->cc = $cc;
		
		if(isset($bcc) && $bcc != '' &&  !is_array($bcc)){$bcc[] = $bcc;}
		$this->bcc = $bcc;

	}

	function send(){

		if($this->from != ''){
			$headers = 'From: ' . $this->from;
		} else {
			$headers = '';
		}
		$headers .= "\nContent-type: text/plain; charset=utf-8\n";

    $send_to = implode(',', $this->to);

		if(mail($send_to, $this->subject, $this->body, $headers)){
			return true;
		} else {
			return false;
		}

	}
}

?>