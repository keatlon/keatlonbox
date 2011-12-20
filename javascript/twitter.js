var twitterClass = function ()
{
	this.OAuthWindow 	=	false;
	this.callbackUrl 	= 	false;
	this.callback		=	false;

	this.configure = function()
	{
	}

	this.connect = function(callback)
	{
		if (typeof callback == 'function')
		{
			twitter.callback	= callback;
		}
		else
		{
			twitter.callback = false;
		}

		if (typeof callback == 'string')
		{
			twitter.callbackUrl = callback;
		}
		else
		{
			twitter.callbackUrl = false;
		}

		twitter.OAuthWindow	=	window.open('/twitter/login', "twitterOAuth", "resizable=1,status=0, toolbar=0, location=0, menubar=0, scrollbars=1,width=800, height=450")
	}

	this.reconnect = function(url, callback)
	{
		if (url)
		{
			ajax.put(url);
			twitter.connect(callback);
		}
	}

	this.connected = function()
	{
		twitter.OAuthWindow.close();

		if (twitter.callback)
		{
			twitter.callback();
		}

		if (twitter.callbackUrl)
		{
			ajax.put(twitter.callbackUrl);
		}

	}

}

var twitter = new twitterClass();
