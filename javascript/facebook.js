var facebookClass = function ()
{
    this.configure = function(options)
   	{
        FB.init({
            appId      : options.id,
            channelUrl : options.domain + '/channel.html',
            status     : true,
            cookie     : true,
            oauth      : true,
            xfbml      : true,
			frictionlessRequests:true
        });
    }

	this.connect = function(perms, callback)
	{
        FB.login(function(response)
        {
           if (response.authResponse)
           {
               ajax.put('/account/signin', {}, function (response){
                    if (typeof callback != 'undefined')
                    {
                        callback(response);
                    }
                });
           }

         }, {scope: perms});
	}


	this.disconnect = function ()
	{
		FB.getLoginStatus(this._disconnect);
	}

	this._disconnect = function (response)
	{
		if (response.status)
		{
			FB.logout(function ()
			{
				location.href = '/account/signout';
			});
		}
		else
		{
			location.href = '/account/signout';
		}
	}

}
var facebook = new facebookClass();