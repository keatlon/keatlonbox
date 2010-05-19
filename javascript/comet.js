var cometClass = function()
{
	this.options = {
		'put_url'	:	'/publish',
		'get_url'	:	'/activity'
	};

	this.subscribers	= new Array();
	this.statuses		= new Array();

	this.init = function ()
	{
	};

	this.errorHandler = function (data)
	{
        console.log('Comet Transport Error', data);
	};

	this.stop = function (channelId)
	{
		this.statuses.push(channelId);
	}

	this.start = function (channelId)
	{
		comet.statuses.push(channelId);

		$.get(comet.options.get_url + '?id=' + channelId, function(response){

			comet.dispatch(channelId, response);

			if ($.inArray(channelId, comet.statuses))
			{
				comet.start(channelId);
			}
		});
	}

	this.put = function (channelId, params)
	{
		$.post(comet.options.put_url + '?id=' + channelId, params, function(response){});
	}

	this.subscribe = function (channelId, cb)
	{
		var subscriber = {'channelId' : channelId, 'cb' : cb};
		comet.subscribers.push(subscriber);
	}

	this.dispatch = function (channelId, params)
	{
		for(var l in this.subscribers)
		{
			if (comet.subscribers[l].channelId == channelId)
			{
				comet.subscribers[l].cb(channelId, params);
			}
		}
	}

};

var comet = new cometClass();