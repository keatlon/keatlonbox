<?php
class authFacebook extends authBase
{
	/*
	 * instanceof Facebook
	 */
	static protected $instance;

	static function i()
	{
		if (!self::$instance)
		{
			fb::init();
			self::$instance	= new Facebook(conf::i()->facebook['apikey'], conf::i()->facebook['secret']);
		}

		return self::$instance;
	}

    function authorize($data)
	{
		$fbUserId		= self::i()->get_loggedin_user();

		if (!$fbUserId)
		{
			return false;
		}

		$fbUser = userFacebookPeer::getItem(userFacebookPeer::getList(array('facebook_id' => $fbUserId)));

		if (!$fbUser)
		{
			$this->createUser($fbUserId);
			$fbUser = userFacebookPeer::getItem(userFacebookPeer::getList(array('facebook_id' => $fbUserId)));
		}

		$this->setCredentials($fbUser['id']);

		auth::setGateway('facebook');

        return $fbUser['id'];
    }

    function clearCredentials()
	{
		parent::clearCredentials();
		// self::i()->logout( conf::i()->domains['web'] . $_SERVER['REQUEST_URI'] );
	}

	function createUser($fbUserId)
	{
		try
		{
			$facebook = self::i();

			$profileData	= $facebook->api_client->users_getInfo($fbUserId, 'last_name, first_name, profile_url, website, about_me, current_location');
			$profileAlbumId = array_shift(db::cols('SELECT (:uid << 32) + (-3 & 0xffffffff)', array('uid' => $fbUserId)));
			$profilePhotos	= $facebook->api_client->photos_get('', $profileAlbumId, '');

			$user['status']          = 'active';
			$user['gate']            = 'facebook';
			$user['email']           = '';
			$user['password']        = '';

			$userId = userPeer::insert($user);

			$userData['id']			=	$userId;
			$userData['name']		=	$profileData[0]['first_name'] . ' ' . $profileData[0]['last_name'];
			$userData['facebook']	=	$profileData[0]['profile_url'];
			$userData['activity']	=	$profileData[0]['about_me'];
			$userData['web']		=	$profileData[0]['website'];
			$userData['image_id']	=	imageStorage::save($profilePhotos[0]['src_big']);


			userDataPeer::insert($userData);
			userFacebookPeer::insert(array('id' => $userId, 'facebook_id' => $fbUserId));
		}
		catch (FacebookRestClientException $e)
		{
			switch($e->getCode())
			{
				case 13:
					break;

				case API_EC_PARAM_SESSION_KEY:
					$facebook->clear_cookie_state();
					break;
			}
		}
	}

}
