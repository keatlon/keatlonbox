var vkontakteClass = function ()
{

	this.connect = function(perms, callback, url)
	{
		if (typeof url == 'undefined')
		{
			var url = '/account/signin';
		}

		VK.Auth.login(function (response){

		}, perms);

        FB.login(function(response)
        {
           if (response.authResponse)
           {
               ajax.put(url, {}, function (response){
                    if (typeof callback == 'function')
                    {
                        callback(response);
                    }
                });
           }

         }, {scope: perms});
	}


	this.disconnect = function (url)
	{
		if (typeof url == 'undefined')
		{
			var url = '/account/signout';
		}

		FB.getLoginStatus(function (response) { this._disconnect(response, url) } );
	}

	this._disconnect = function (response, url)
	{
		if (response.status && response.status != 'unknown')
		{
			FB.logout(function ()
			{
				location.href = url;
			});
		}
		else
		{
			location.href = url;
		}
	}

}
var vkontakte = new vkontakteClass();