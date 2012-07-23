<?
class email
{
	static public function send($emailTo, $subject, $body)
	{
		mb_language('uni');
		
		$headers = "Mime-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/html;charset=utf-8" . "\r\n";
		$headers .= "From: " . conf::$conf['email']['name_from'] . "<" . conf::$conf['email']['email_from'] . ">" . "\r\n";
		$headers .= "Return-Path: " . conf::$conf['email']['return_path'] . "\r\n";

		return mb_send_mail($emailTo, $subject, $body, $headers, '-f' . conf::$conf['email']['return_path']);
	}
}
