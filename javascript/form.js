var errorRenderer = function()
{
	this.ER_SUPPRESED   = 0;
	this.ER_CONTAINER   = 1;
	this.ER_FIELDS      = 2;
};

var Form = function( form_name, multipart, method )
{
	// Constructor
	this.form_namespace = form_name;
	this.formJObject    = $('#' + this.form_namespace + '_form');
	this.multipart      = ( typeof multipart == 'undefined' ) ? false : multipart;

    if (typeof method == 'undefined')
    {
        var method = 'post';
    }

    if (method == 'get')
    {
        this.multipart = false;
    }

	this.lastResponseData = null;
	this.steps = $('.step', this.formJObject).length;
	this.current_step = 1;
	if (this.steps)
	{
		$('.step', this.formJObject).eq(0).show();
	}

	this.onSuccess		= null
	this.onError		= null;
	this.onSuccessStep	= null;
	this.onBeforeSubmit = null;

	var errRenderer = new errorRenderer();

	this.errorRendererType = errRenderer.ER_FIELDS;
	this.errorRendererContainer = 'error_container';

	this.setErrorRenderer = function (rendererType, container)
	{
		this.errorRendererType = rendererType;
		this.errorRendererContainer = typeof ( container == 'undefined') ? this.errorRendererContainer : container;
	}

	this.bind = function ( module, action )
	{
		var thisForm	= this;
		var step		= false;
		var step_url	= '';

		if ( this.current_step < this.steps )
		{
			step_url = '&step=' + this.current_step;
		}

        this.formJObject.ajaxForm( {
            url         : application.url( module, action ),
            dataType    : 'json',
            type        : method,
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

                if (method == 'get')
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

                disableSubmit();
            },

            success: function ( response )
            {
        		thisForm.hideErrors();

                if (typeof response != 'object')
                {
                    ajax.errorHandler(response.toString());
                    enableSubmit();
                    return;
                }

                if ( response.status == 'error' )
                {
                    if ( typeof thisForm.onError == 'function' )
                    {
                        thisForm.onError( response );
                    }
                    
                    thisForm.showErrors(response);
                }

                if ( response.status == 'success' && typeof thisForm.onSuccess == 'function')
                {
                    thisForm.onSuccess( response );
                }

                if (response.status == 'exception')
                {
                    console.log('Exception', response.errors);
                }

                enableSubmit();


            }
        } );
	}

	var disableSubmit = function()
	{
		$('input:submit', this.formJObject).attr('disabled', true);
		$('button:submit', this.formJObject).attr('disabled', true);
	};

	var enableSubmit = function()
	{
		$('input:submit', this.formJObject).attr('disabled', false);
		$('button:submit', this.formJObject).attr('disabled', false);
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
				$('#' + this.form_namespace + '_' + fieldName + '_error').html(response.errors[fieldName]);
				$('#' + this.form_namespace + '_' + fieldName + '_error').show(200);
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
				$('#' + this.form_namespace + '_' + fieldName + '_error').hide();
			}
		}
	};
};
