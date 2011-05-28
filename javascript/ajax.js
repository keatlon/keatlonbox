var ajaxClass = function()
{
	this.init = function ()
	{
		$('body').ajaxStart(function()
		{
			$('#ajax_wrapper').fadeIn(200);
			$('#ajax_loading').show();
		});

		$("body").ajaxComplete(function(e, xhr, settings)
		{
            $('#ajax_loading').hide();
			$('#ajax_wrapper').hide(100);

			var response	=	jQuery.parseJSON(xhr.responseText);

			if(typeof response.jsonredirect != 'undefined')
			{
				if (typeof enableAjaxNavigation != 'undefined' && enableAjaxNavigation)
				{
					if ($.address)
					{
						$.address.value(response.jsonredirect);
					}
					else
					{
						location.href = response.jsonredirect;
					}
				}
				else
				{
					location.href = response.jsonredirect;
				}
			}

			if(typeof response.redirect != 'undefined')
			{
				location.href = response.redirect;
			}

			if (response.notice)
			{
				notification.success(response.notice);
			}

			if (response.status == 'exception')
			{
				console.log('Exception', response.errors);
			}

			if (response.status == 'error')
			{
				console.log('Error', response.errors);
			}

		});

		$("body").ajaxError(function(event, request, settings){
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

			if (datatype == 'json' && response.status == 'exception')
			{
				 console.log('Exception', response.errors);
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
			
			if (datatype == 'json' && response.status == 'exception')
			{
				   console.log('Exception', response.errors);
			}

		}, datatype);
	}

	this.put = function (url, params, callback, datatype)
	{
		if (typeof datatype == 'undefined')
		{
			datatype = 'json';
		}

		$.post(url, params, function(response){

			if ( typeof callback == 'function')
			{
				callback(response);
			}

		}, datatype);
	}
};

var ajax = new ajaxClass();