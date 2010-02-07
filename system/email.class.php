<?
class email
{
    static public function send( $name_to, $email_to, $subject, $body, $id = false )
    {
        $name_from      = conf::i()->email['name_from'];
        $email_from     = conf::i()->email['email_from'];
        $data_charset   = 'UTF-8';
        $send_charset   = 'UTF-8';


		if (!$id)
		{
			$id = mailPeer::insert(array(
				'name'      => (string)$name_to,
				'processed' => time(),
				'email'     => $email_to,
				'subject'   => $subject,
				'text'      => $body,
			));
		}
		else
		{
            mailPeer::update($id, array('processed' => time()));
		}

        $to         = self::mime_header_encode($name_to, $data_charset, $send_charset) . ' <' . $email_to . '>';
        $subject    = self::mime_header_encode($subject, $data_charset, $send_charset);
        $from       = self::mime_header_encode($name_from, $data_charset, $send_charset) .' <' . $email_from . '>';

        if($data_charset != $send_charset)
        {
            $body = iconv($data_charset, $send_charset, $body);
        }

        $headers = "From: $from\r\n";
        $headers .= "Content-type: text/plain; charset=$send_charset\r\n";
        $headers .= "Return-Path: " . conf::i()->email['return_path'] . "\r\n";

		log::push(log::E_USER, 'EMAIL', 'sending to ' . $email_to);
        $result = mail($email_to, $subject, $body, $headers, '-f' . conf::i()->email['return_path']);

        if (!$result)
        {
			log::push(log::E_USER, 'EMAIL', 'failed sending to ' . $email_to);
            mailPeer::update($id, array('processed' => 0));
        }
        return $result;
    }

    static private function mime_header_encode($str, $data_charset, $send_charset)
    {
        if($data_charset != $send_charset)
        {
            $str = iconv($data_charset, $send_charset, $str);
        }

        return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
    }
}

?>