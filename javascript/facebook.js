var facebookClass = function ()
{
	this.init = function()
	{
		if (typeof FB == 'undefined')
		{
			return false;
		}

		$('body').append('<div id="fb-root"></div>');
		FB.init({appId: app.options.facebook.id, status: true, cookie: true, xfbml: false});
	}

	this.connect = function(perms, callback)
	{
		FB.login(function(response)
		{
			if (response.session)
			{
				callback()
			}
		}, {'perms' : perms});
	}

	this.signin = function ()
	{
		ajax.put('/account/facebook', {'facebook_id':facebook.id()});
	}

	this.id		=	function()
	{
		var session = FB.getSession();
		return session.uid;
	}
}

var facebook = new facebookClass();
