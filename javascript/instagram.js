var instagramClass = function ()
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
			var authorizeUrl = '/instagram/login';
		}

		if (typeof callback == 'function')
		{
			instagram.callback	= callback;
		}
		else
		{
			instagram.callback = false;
		}

		if (typeof callback == 'string')
		{
			instagram.callbackUrl = callback;
		}
		else
		{
			instagram.callbackUrl = false;
		}

		instagram.OAuthWindow	=	window.open(authorizeUrl, "instagramOAuth", "resizable=1,status=0, toolbar=0, location=0, menubar=0, scrollbars=1,width=480, height=640")
	}

	this.reconnect = function(url, callback)
	{
		if (url)
		{
			ajax.put(url);
			instagram.connect(callback);
		}
	}

	this.connected = function()
	{
		instagram.OAuthWindow.close();

		if (instagram.callback)
		{
			instagram.callback();
		}

		if (instagram.callbackUrl)
		{
			ajax.put(instagram.callbackUrl);
		}

	}

}

var instagram = new instagramClass();
