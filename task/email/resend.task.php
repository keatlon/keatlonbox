<?php
class resendEmailController extends taskActionController
{
    public function execute($params)
    {
		$items = mailPeer::getItems(mailPeer::getList(array('processed' => 0)));

		foreach($items as $item)
		{
			echo 'resending to ' . $item['email'] . "\n";
			email::send($item['name'], $item['email'], $item['subject'], $item['text'], $item['id']);
		}

    }
}

?>