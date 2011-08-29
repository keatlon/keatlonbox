<?
class email
{
    static public function send( $nameTo, $emailTo, $subject, $body, $id = false )
    {
		mb_language('uni');
		
        $nameFrom      = conf::i()->email['name_from'];
        $emailFrom     = conf::i()->email['email_from'];

        $to				= $nameTo . ' <' . $emailTo . '>';
        $from			= $nameFrom .' <' . $emailFrom . '>';

		$headers	=	"Mime-Version: 1.0\n";
		$headers	.=	"Content-Type: text/html;charset=UTF-8\n";
		$headers	.=	"From: $from\n";
		$headers	.=	"To: $to\n";
        $headers	.=	"Return-Path: " . conf::i()->email['return_path'] . "\r\n";

        return mb_send_mail($emailTo, $subject, $body, $headers, '-f' . conf::i()->email['return_path']);
    }
}
