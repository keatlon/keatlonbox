var facebookClass = function ()
{
	this.init = function()
	{
		$('body').append('<div id="fb-root"></div>')
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
}

var facebook = new facebookClass();
