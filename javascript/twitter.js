var twitterClass = function ()
{
	this.OAuthWindow 	=	false;
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

		twitter.OAuthWindow	=	window.open('/twitter/login', "twitterOAuth", "resizable=1,status=0, toolbar=0, location=0, menubar=0, scrollbars=1,width=800, height=450")
	}

	this.disconnect = function(callback)
	{
	}

	this.connected = function()
	{
		twitter.OAuthWindow.close();

		if (typeof twitter.callback == 'function')
		{
			twitter.callback();
		}
	}

}

var twitter = new twitterClass();
