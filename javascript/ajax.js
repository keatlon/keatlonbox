var ajaxClass = function()
{
	this.configure = function (config)
	{
		$('body').ajaxStart(function()
		{
		});

		$("body").ajaxError(function(event, request, settings)
		{
            ajax.errorHandler(request.responseText);
		});

	};

	this.errorHandler = function (data)
	{
		if (typeof console != 'undefined')
		{
			console.log('Ajax Transport Error', data);
		}
	};

	this.url = function (url, params, callback, datatype)
	{
		if (typeof datatype == 'undefined')
		{
			datatype = 'json';
		}
		
		$.get(url, params, function(response){

			if (typeof callback == 'function')
			{
				callback(response);
			}

		}, datatype);
	}

	this.get = function (url, params, callback, renderer)
	{
		if (typeof renderer == 'undefined')
		{
			renderer = 'dialog';
		}

		$.ajax(url,
		{
			url			:	url,
			type		:	'GET',
			data 		:	params,
			dataType	:	'json',
			headers		:	{ 'KBox-Render' : renderer},
			success		:	function(response, textStatus, jqXHR)
			{
				application.dispatch(response);

				if (typeof callback == 'function')
				{
					callback(response);
				}
			}

		})


	}

	this.put = function (url, params, callback, datatype)
	{
		var ajaxParams	=	jQuery.extend({}, params);
		
		if (typeof datatype == 'undefined')
		{
			datatype = 'json';
		}

		if (typeof ajaxParams == 'undefined')
		{
			ajaxParams	=	{};
		}
		
		ajaxParams.context = application.getGlobalContext();


		for(var l in ajaxParams)
		{
			if (typeof ajaxParams[l] == "function")
			{
				delete ajaxParams[l];
			}
		}

		$.post(url, ajaxParams, function(response){

			application.dispatch(response);

			if ( typeof callback == 'function')
			{
				callback(response);
			}

		}, datatype);
	}
};

var ajax = new ajaxClass();