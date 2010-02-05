<?php
class authTwitter extends authBase
{
    function authorize($data)
	{
		if (!$_SESSION['access_token'])
		{
			return false;
		}
    }

	function getAuthToken($data)
	{
		twitter::init();

		$connection		= new TwitterOAuth(conf::i()->twitter['key'], conf::i()->twitter['secret']);
		$request_token	= $connection->getRequestToken(conf::i()->domains['web'] . '/twitter');

		$_SESSION['oauth_token'] = $token	= $request_token['oauth_token'];
		$_SESSION['oauth_token_secret']		= $request_token['oauth_token_secret'];

		if($connection->http_code == 200)
		{
			return http::redirect($connection->getAuthorizeURL($token));
		}

		return false;
	}

	function getAccessToken($data)
	{
		if (isset($data['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token'])
		{
			$this->clearCredentials();
		}

		twitter::init();

		$connection		= new TwitterOAuth(conf::i()->twitter['key'], conf::i()->twitter['secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access_token	= $connection->getAccessToken($_REQUEST['oauth_verifier']);

		$_SESSION['access_token'] = $access_token;

		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);

		if (200 == $connection->http_code)
		{
			$user = userTwitterPeer::getItem(userTwitterPeer::getList(array('twitter_id' => $access_token['user_id'])));

			if (!$user)
			{
				$this->createUser($access_token['user_id']);
				$user = userTwitterPeer::getItem(userTwitterPeer::getList(array('twitter_id' => $access_token['user_id'])));
			}

			$this->setCredentials($user['id']);
			auth::setGateway('twitter');

			return $user['id'];
		}
		else
		{
			$this->clearCredentials();
		}
	}

	function createUser($twitterUserId)
	{
		$xml = simplexml_load_file('http://twitter.com/users/show.xml?user_id=' . $twitterUserId);

		$user['status']          = 'active';
		$user['gate']            = 'twitter';
		$user['email']           = '';
		$user['password']        = '';

		$userId = userPeer::insert($user);

		$userData['id']			=	$userId;
		$userData['name']		=	$xml->name;
		$userData['twitter']	=	$xml->screen_name;
		$userData['activity']	=	$xml->description;
		$userData['web']		=	$xml->url;
		$userData['image_id']	=	imageStorage::save($xml->profile_image_url);

		userDataPeer::insert($userData);
		userTwitterPeer::insert(array('id' => $userId, 'twitter_id' => $twitterUserId));
	}

}
?>

