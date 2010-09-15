var ajaxClass = function()
{
	this.init = function ()
	{
		$('body').ajaxStart(function(){
			$('#ajax_wrapper').fadeIn(200);
			$('#ajax_loading').show();
		 });

		$("body").ajaxComplete(function(e, XMLHttpRequest, ajaxOptions){
            $('#ajax_loading').hide();
			$('#ajax_wrapper').hide(100);

			var response = $.httpData(XMLHttpRequest, ajaxOptions.dataType);

			if(typeof response.jsonredirect != 'undefined')
			{
				if ($.address)
				{
					$.address.value(response.redirect);
				}
				else
				{
					location.href = response.redirect;
				}
			}

			if(typeof response.redirect != 'undefined')
			{
				location.href = response.redirect;
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

			if (datatype == 'json')
			{
				if (response.status == 'success' && typeof callback != 'function' && response.message)
				{
						notification.success(response.message);
				}

				if (response.status == 'exception')
				{
					console.log('Exception', response.errors);
				}

				if (response.status == 'error')
				{
					console.log('Error', response.errors);
				}
			}

		}, datatype);
	}
};

var ajax = new ajaxClass();