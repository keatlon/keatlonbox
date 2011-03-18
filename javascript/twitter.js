var twitterClass = function ()
{
	this.OAuthWindow = false;

	this.init = function()
	{
	}

	this.connect = function(callback)
	{
		if (typeof callback == 'undefined')
		{
			callback	=	'twitter.connected';
		}

		twitter.OAuthWindow	=	window.open('/twitter/login?cb=' + callback, "twitterOAuth", "resizable=1,status=0, toolbar=0, location=0, menubar=0, scrollbars=1,width=800, height=380")
	}

	this.disconnect = function(callback)
	{
	}

	this.connected = function()
	{
		twitter.OAuthWindow.close();
	}

}

var twitter = new twitterClass();
