var cometClass = function()
{
	this.options = {
		'enabled'	:	false,
		'put_url'	:	'',
		'get_url'	:	''
	};

	this.subscribers	= new Array();
	this.statuses		= new Array();

	this.init = function ()
	{
		if(typeof app.comet != 'undefined')
		{
			this.options = app.comet;
		}
	};

	this.errorHandler = function (data)
	{
		if (!comet.options.enabled)
		{
			return false;
		}
		
		return true;
	};

	this.stop = function (channelId)
	{
		if (!comet.options.enabled)
		{
			return false;
		}

		comet.statuses = $.grep(comet.statuses, function(v) { return v != channelId; });
		return true;
	}

	this.start = function (channelId)
	{
		if (!comet.options.enabled)
		{
			return false;
		}

		comet.statuses.push(channelId);

		$.getJSON(comet.options.get_url + '?id=' + channelId, function(response){

			comet.dispatch(channelId, response);

			if ($.inArray(channelId, comet.statuses) > -1)
			{
				comet.start(channelId);
			}
		});

		return true;
	}

	this.put = function (channelId, params)
	{
		return $.post(comet.options.put_url + '?id=' + channelId, params, function(response){});
	}

	this.subscribe = function (channelId, hash, cb)
	{
		var subscriber = {'channelId' : channelId, 'hash':hash, 'cb' : cb};
		return comet.subscribers.push(subscriber);
	}

	this.dispatch = function (channelId, params)
	{
		for(var l in this.subscribers)
		{
			if ( (comet.subscribers[l].channelId == channelId) && (params.hash == comet.subscribers[l].hash) )
			{
				comet.subscribers[l].cb(channelId, params);
			}
		}
		return true;
	}

};

var comet = new cometClass();