var errorRenderer = function()
{
	this.ER_SUPPRESED   = 0;
	this.ER_CONTAINER   = 1;
	this.ER_FIELDS      = 2;
};

var Form = function(f)
{
	this.f			=	f;
	this.multipart  =	$(':file', this.f).length > 0;
	this.method		=	$(this.f).attr('method');

	if (this.method == '')
	{
		this.method = 'post';
	}

	this.lastResponseData = null;
	this.onBeforeSubmit = null;

	var errRenderer = new errorRenderer();

	this.errorRendererType = errRenderer.ER_FIELDS;
	this.errorRendererContainer = 'error_container';

	this.setErrorRenderer = function (rendererType, container)
	{
		this.errorRendererType = rendererType;
		this.errorRendererContainer = typeof ( container == 'undefined') ? this.errorRendererContainer : container;
	}

	this.url2key = function (url)
	{
		return url.substring(1).split('/').join('_');
	}

	this.url2method = function (url)
	{
		var parts	=	url.substring(1).split('/');
		var method	=	'';
		
		for (var l in parts)
		{
			if (l == 0)
			{
				method = parts[l];
				continue;
			}

			method = method + parts[l].substring(0, 1).toUpperCase() + parts[l].substring(1, parts[l].length);
		}

		return method;
	}

	this.init = function ()
	{
		var thisForm		= this;
		var errorSelector	= false;

		$(':input,:file,', this.f).not('[type=submit],[type=hidden]').each(function(){

			errorSelector	=	thisForm.url2key(thisForm.f.attr('action')) + '_' + thisForm.exractFieldName($(this).attr('name')) + '_error';

			if (!$('#' + errorSelector).length)
			{
				$('<div class="error"></div>').attr('id', errorSelector).insertAfter(this);
			}
		});

        this.f.ajaxForm( {
            url         : $(thisForm).attr('action'),
            dataType    : 'json',
            type        : thisForm.method,
            iframe      : false,

            beforeSubmit: function (data, jobj, opt)
			{

                if ( typeof thisForm.onBeforeSubmit == 'function')
                {
                    if (!thisForm.onBeforeSubmit())
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

                if ($(thisForm).method == 'get')
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

                thisForm.disableSubmit();
            },

            success: function ( response )
            {
        		thisForm.hideErrors();

                if (typeof response != 'object')
                {
                    ajax.errorHandler(response.toString());
                    thisForm.enableSubmit();
                    return;
                }

				var successMethod	= thisForm.url2method(thisForm.f.attr('action')) + 'Success';
				var errorMethod	= thisForm.url2method(thisForm.f.attr('action')) + 'Error';

                if ( response.status == 'success')
                {
                    eval( ' if (typeof ' + successMethod + ' == "function") ' + successMethod + '( response )');
                }

                if ( response.status == 'error')
                {
                    thisForm.showErrors(response);
                    eval( ' if (typeof ' + errorMethod + ' == "function") ' + errorMethod + '( response )');
                }

                thisForm.enableSubmit();
            }
        } );
	}

	this.disableSubmit = function()
	{
		$('input:submit', this.f).attr('disabled', true);
		$('button:submit', this.f).attr('disabled', true);
	};

	this.enableSubmit = function()
	{
		$('input:submit', this.f).attr('disabled', false);
		$('button:submit', this.f).attr('disabled', false);
	};

	this.showErrors = function(response)
	{
		this.lastResponseData = response;

		var errors = '';
		
		var eRenderer = new errorRenderer();

		if ( typeof response.errors != 'undefined' )
		{
			for ( var fieldName in response.errors )
			{
				$('#' + this.url2key(this.f.attr('action')) + '_' + fieldName + '_error').html(response.errors[fieldName]);
				$('#' + this.url2key(this.f.attr('action')) + '_' + fieldName + '_error').show();
			}

			return;
		}
	};

	this.hideErrors = function()
	{
		if ( this.lastResponseData == null) return;
		
		var eRenderer = new errorRenderer();

		if ( typeof this.lastResponseData.errors != 'undefined')
		{
			for ( var fieldName in this.lastResponseData.errors )
			{
				$('#' + this.url2key(this.f.attr('action')) + '_' + fieldName + '_error').hide();
			}
		}
	};

	this.exractFieldName = function(n)
	{
		var r = new RegExp(/\[(.*)\]/g).exec(n);
		if (r == null)
		{
			return n;
		}

		return r[1];
	};

	this.init();
};


function autobindForms(selector)
{
	if (typeof selector == 'undefined')
	{
		selector	=	'form[action]';
	}

	$(selector).each(function(){
		new Form($(this));
	});
}
