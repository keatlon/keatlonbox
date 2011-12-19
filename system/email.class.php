<?
class email
{
	static public function send($emailTo, $subject, $body)
	{
		mb_language('uni');

		$headers = "Mime-Version: 1.0\n";
		$headers .= "Content-Type: text/html;charset=utf-8\n";
		$headers .= "From: " . conf::i()->email['name_from'] . "<" . conf::i()->email['email_from'] . ">\n";
		$headers .= "Return-Path: " . conf::i()->email['return_path'] . "\n";

		return mb_send_mail($emailTo, $subject, $body, $headers, '-f' . conf::i()->email['return_path']);
	}
}
