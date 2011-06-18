var facebookClass = function ()
{
	this.init = function(config)
	{
	}

	this.connect = function(perms, callback)
	{
	}

	this.signin = function (callback)
	{
		dialog.load('/account/waiting');
		ajax.put('/account/signin', {'mode' : 'facebook', 'facebook_id' : facebook.id()}, function (response){

			dialog.close();
			
			if (typeof callback != 'undefined')
			{
				callback(response);
			}
			
		});
	}

	this.signup = function (callback)
	{
		dialog.load('/account/waiting');
		
		ajax.put('/account/signup', {'mode' : 'facebook', 'facebook_id' : facebook.id(), 'i': $('.invitation-code').val() }, function (response){

			dialog.close();
			
			if (typeof callback != 'undefined')
			{
				callback(response);
			}

		});
	}

	this.signout = function ()
	{
		FB.logout(function()
		{
			location.href='/account/signout';
		});
	}

	this.id		=	function()
	{
	}
}

var facebook = new facebookClass();
