(function($)
{
	$.fn.form = function(method)
	{
		var methods	=
		{
			init				:	function (settings)
			{
				return this.each(function()
				{
					var data	=	$(this).data('form');

					if (data)
					{
						return;
					}

					var element	=	$(this);

					var defaults	=
					{
						multipart		:	$(':file', element).length > 0,
						method			:	$(element).attr('method') ? $(element).attr('method') : 'POST',
						action			:	$(element).attr('action'),
						response		:	null,
						onBeforeSubmit	:	null,
						onSuccess		:	null,
						onError			:	null
					};

					var options		=	$.extend(defaults, settings);
					
					$(':input,:file', element).not('[type=submit],[type=hidden]').each(function(){

						errorSelector	=	application.getErrorSelector(options.action, application.exractFieldName($(this).attr('name')));

						if (!$('#' + errorSelector).length)
						{
							$('<div class="error"></div>').attr('id', errorSelector).insertAfter(this);
						}
					});

					$(element).ajaxForm( {
						url         :	options.action,
						type        :	options.method,
						iframe      :	options.multipart,
						dataType    :	'json',

						beforeSubmit: function (data, jobj, opt)
						{
							if ( typeof $(element).data('form').onBeforeSubmit == 'function')
							{
								if (!$(element).data('form').onBeforeSubmit())
								{
									return false;
								}
							}

							/*
							* Remove default value for input text
							* */
							for(var l in data)
							{
								var obj = $('input[type=text][name="' + data[l].name + '"]');

								if (obj.length > 0)
								{
									if ( $(obj).attr('title') != '' && $(obj).attr('title') == obj.val())
									{
										data[l].value = '';
									}
								}
							}

							if (options.method == 'get')
							{
								var url = '';

								for(l in data)
								{
									url = url + '/' + data[l].name + '/' + data[l].value;
								}

								this.url = opt.url + url;

									location.href = this.url;

								return false;
							}

							$(element).form('disableSubmit');
						},

						success: function ( response )
						{
							$(element).form('hideErrors');

							if (typeof response != 'object')
							{
								ajax.errorHandler(response.toString());
								$(element).form('enableSubmit');
								return;
							}

							if ( response.status == 'success')
							{
								if ($(element).data('form').onSuccess)
								{
									$(element).data('form').onSuccess(response);
								}
							}

							if ( response.status == 'error')
							{
								$(element).form('showErrors', response);

								if ($(element).data('form').onError)
								{
									$(element).data('form').onError(response);
								}
							}

							$(element).form('enableSubmit');
						}
					} );

					$(element).data('form', options);
				});
				

			},

			options				:	function (settings)
			{
				return this.each(function()
				{
					$(this).data('form', $.extend($(this).data('form'), settings));
				});
			},

			disableSubmit		:	function()
			{
				return this.each(function()
				{
					$('input:submit', $(this)).attr('disabled', true);
					$('button:submit', $(this)).attr('disabled', true);
				});
			},


			enableSubmit		:	function()
			{
				return this.each(function()
				{
					$('input:submit', $(this)).attr('disabled', false);
					$('button:submit', $(this)).attr('disabled', false);
				});
			},


			showErrors			:	function(response)
			{
				return this.each(function()
				{
					$(this).data().form.response = response;

					var errors = '';

					if ( typeof response.errors != 'undefined' )
					{
						for ( var fieldName in response.errors )
						{
							$('#' + application.getErrorSelector($(this).data().form.action, fieldName)).html(response.errors[fieldName]).show();
						}

						return;
					}
				});
			},

			hideErrors		:	function()
			{
				
				return this.each(function()
				{
					if ( $(this).data().form.response == null) return;

					if ( typeof $(this).data().form.response.errors != 'undefined')
					{
						for ( var fieldName in $(this).data().form.response.errors )
						{
							$('#' + application.getErrorSelector($(this).data().form.action, fieldName)).hide();
						}
					}
				});
			}

		};


		if ( methods[method] )
		{
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		}
			else if ( typeof method === 'object' || ! method )
		{
			return methods.init.apply( this, arguments );
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
		}

	};

})(jQuery);