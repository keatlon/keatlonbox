var twitterClass = function ()
{
	this.OAuthWindow 	=	false;
	this.callbackUrl 	= 	false;
	this.callback		=	false;

	this.configure = function()
	{
	}

	this.connect = function(callback, authorizeUrl)
	{
		if (typeof authorizeUrl == 'undefined')
		{
			var authorizeUrl = '/twitter/login';
		}

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

		twitter.OAuthWindow	=	window.open(authorizeUrl, "twitterOAuth", "resizable=1,status=0, toolbar=0, location=0, menubar=0, scrollbars=1,width=800, height=auto")
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
