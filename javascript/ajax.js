var ajaxClass = function()
{
	this.configure = function (config)
	{
		$('body').ajaxStart(function()
		{
			$('#ajax_wrapper').fadeIn(200);
			$('#ajax_loading').show();
		});

		$("body").ajaxError(function(event, request, settings)
		{
            ajax.errorHandler(request.responseText);
		});

	};

	this.errorHandler = function (data)
	{
        console.log('Ajax Transport Error', data);
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
			headers		:	{ 'KBox-Renderer' : renderer},
			success		:	function(response, textStatus, jqXHR)
			{
				if (typeof callback == 'function')
				{
					callback(response);
				}

				application.dispatch(response);
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

			if ( typeof callback == 'function')
			{
				callback(response);
			}
			
			application.dispatch(response);

		}, datatype);
	}
};

var ajax = new ajaxClass();