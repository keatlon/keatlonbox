var ajaxClass = function()
{
	this.configure = function (config)
	{
		$('body').ajaxStart(function()
		{
			$('#ajax_wrapper').fadeIn(200);
			$('#ajax_loading').show();
		});

		$("body").ajaxComplete(function(e, xhr, settings)
		{
			var response	=	jQuery.parseJSON(xhr.responseText);

			if(typeof response.jsonredirect != 'undefined')
			{
				location.href = response.jsonredirect;
			}

			if(typeof response.redirect != 'undefined')
			{
				location.href = response.redirect;
			}

			if (response.notice)
			{
				notification.success(response.notice);
			}

			if (response.warning)
			{
				notification.warning(response.warning);
			}

			if (response.status == 'exception')
			{
				console.log('Exception', response.errors);
			}

			if (response.status == 'error')
			{
				console.log('Error', response.errors);
			}

			application.dispatch(response);

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

	this.get = function (url, params, callback, datatype)
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
			
			application.dispatch(response);

		}, datatype);
	}

	this.put = function (url, params, callback, datatype)
	{
		if (typeof datatype == 'undefined')
		{
			datatype = 'json';
		}

		if (typeof params == 'undefined')
		{
			params	=	{};
		}
		
		params.context = application.getGlobalContext();

		$.post(url, params, function(response){

			if ( typeof callback == 'function')
			{
				callback(response);
			}

		}, datatype);
	}
};

var ajax = new ajaxClass();