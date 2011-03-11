var facebookClass = function ()
{
	this.init = function()
	{
		if (typeof FB == 'undefined')
		{
			return false;
		}

		$('body').append('<div id="fb-root"></div>');

		FB.Event.subscribe('auth.login', function(response) {
		});

		FB.Event.subscribe('auth.logout', function(response) {
		});

		FB.Event.subscribe('auth.statusChange', function(response) {
		});

		FB.Event.subscribe('auth.sessionChange', function(response) {
		});


		FB.Event.subscribe('edge.create', function(response) {
		});

		FB.Event.subscribe('edge.remove', function(response) {
		});

		FB.Event.subscribe('comments.add', function(response) {
		});

		FB.Event.subscribe('fb.log', function(response) {
		});

		FB.Event.subscribe('xfbml.render', function() {
		});

		FB.init({appId: app.options.facebook.id, status: true, cookie: true, xfbml: true});

		FB.getLoginStatus(function(response) {
			if (response.session)
			{
				if(facebookId && (response.session.uid != facebookId))
				{
					ajax.put('/account/signout', {'redirect' : false}, function (response){
						location.reload();
					})
					return false;
				}
			}
			else
			{
				if (facebookId)
				{
					ajax.put('/account/signout', {'redirect' : false}, function (response){
						location.reload();
					})
				}
			}
		});
		
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
		var session = FB.getSession();
		return session.uid;
	}

	this.personalRequest	=	function(id, message, callback)
	{
		FB.ui({
			method	:	'apprequests',
			to		:	id,
			message	:	message
		}, callback);
	}


}

var facebook = new facebookClass();
