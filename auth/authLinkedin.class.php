<?php
class authLinkedin extends authBase
{
    function authorize($data)
	{
		if (!$_SESSION['li_access_token'])
		{
			return false;
		}
    }

	function getAuthToken($data)
	{
		linkedin::init();
		$connection		= new LinkedinOAuth(conf::i()->linkedin['key'], conf::i()->linkedin['secret']);
		$request_token	= $connection->getRequestToken(conf::i()->domains['web'] . '/linkedin');

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

		linkedin::init();

		$connection		= new LinkedinOAuth(conf::i()->linkedin['key'], conf::i()->linkedin['secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access_token	= $connection->getAccessToken($_REQUEST['oauth_verifier']);

		$_SESSION['li_access_token'] = $access_token;

		$r = $connection->get('https://api.linkedin.com/v1/people/~', $parameters);

		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);

		if (200 == $connection->http_code)
		{
			$user = userLinkedinPeer::getItem(userLinkedinPeer::getList(array('linkedin_id' => $access_token['user_id'])));

			if (!$user)
			{
				$this->createUser($access_token['user_id']);
				$user = userLinkedinPeer::getItem(userLinkedinPeer::getList(array('linkedin_id' => $access_token['user_id'])));
			}

			$this->setCredentials($user['id']);
			auth::setGateway('linkedin');

			return $user['id'];
		}
		else
		{
			$this->clearCredentials();
		}
	}

	function createUser($linkedinUserId)
	{
		// $xml = simplexml_load_file('http://linkedin.com/users/show.xml?user_id=' . $linkedinUserId);

		$user['status']          = 'active';
		$user['gate']            = 'linkedin';
		$user['email']           = '';
		$user['password']        = '';

		$userId = userPeer::insert($user);

		$userData['id']			=	$userId;
		//$userData['name']		=	$xml->name;
		//$userData['linkedin']	=	$xml->screen_name;
		//$userData['activity']	=	$xml->description;
		//$userData['web']		=	$xml->url;
		//$userData['image_id']	=	imageStorage::save($xml->profile_image_url);

		userDataPeer::insert($userData);
		userLinkedinPeer::insert(array('id' => $userId, 'linkedin_id' => $linkedinUserId));
	}

}
?>

