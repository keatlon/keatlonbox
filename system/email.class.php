<?
class email
{
	static public function send($emailTo, $subject, $body, $fromEmail = false, $fromName = false, $extra = array())
	{
		mb_language('uni');

		$fromEmail	=	$fromEmail 	?	$fromEmail	:	conf::$conf['email']['email_from'];
		$fromName	=	$fromName 	?	$fromName	:	conf::$conf['email']['name_from'];

		$headers 	=	"Mime-Version: 1.0" . "\r\n";
		$headers 	.=	"Content-Type: text/html;charset=utf-8" . "\r\n";
		$headers 	.=	"From: " . $fromName . "<" . $fromEmail . ">" . "\r\n";
		$headers 	.=	"Return-Path: " . conf::$conf['email']['return_path'] . "\r\n";

		return mb_send_mail($emailTo, $subject, $body, $headers, '-f' . conf::$conf['email']['return_path']);
	}
}
