<?
class email
{
	static public function send($nameTo, $emailTo, $subject, $body, $id = false)
	{
		log::push(log::E_USER, 'send email to ' . $emailTo . $subject);

		mb_language('uni');

		$headers = "Mime-Version: 1.0\n";
		$headers .= "Content-Type: text/html;charset=utf-8\n";
		$headers .= "Return-Path: " . conf::i()->email['return_path'] . "\\n";

		return mb_send_mail($emailTo, $subject, $body, $headers, '-f' . conf::i()->email['return_path']);
	}
}
