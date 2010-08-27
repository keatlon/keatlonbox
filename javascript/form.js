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
	this.id			=	$(this.f).attr('id');

	if (this.method == '')
	{
		this.method = 'post';
	}

	this.lastResponseData = null;

	this.onSuccess		= $(this.f).attr('onsuccess');
	this.onError		= $(this.f).attr('onerror');
	this.onBeforeSubmit = null;

	var errRenderer = new errorRenderer();

	this.errorRendererType = errRenderer.ER_FIELDS;
	this.errorRendererContainer = 'error_container';

	this.setErrorRenderer = function (rendererType, container)
	{
		this.errorRendererType = rendererType;
		this.errorRendererContainer = typeof ( container == 'undefined') ? this.errorRendererContainer : container;
	}

	this.init = function ()
	{
		var thisForm	= this;

		$(':input,:file,', this.f).not('[type=submit],[type=hidden]').each(function(){

			$('<div class="error"></div>').attr('id', thisForm.f.attr('id') + '_' + thisForm.exractFieldName($(this).attr('name')) + '_error').insertAfter(this);
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

                if ( response.status == 'error' )
                {
                    if ( typeof thisForm.onError == 'function' )
                    {
						eval(thisForm.onError + '( response )');
                    }
                    
                    thisForm.showErrors(response);
                }

                if ( response.status == 'success' && typeof thisForm.onSuccess == 'string')
                {
                    eval(thisForm.onSuccess + '( response )');
                }

                if (response.status == 'exception')
                {
                    console.log('Exception', response.errors);
                }

                thisForm.enableSubmit();

				if (typeof response.notice != 'undefined')
				{
					notification.success(response.notice);
				}
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
				$('#' + this.id + '_' + fieldName + '_error').html(response.errors[fieldName]);
				$('#' + this.id + '_' + fieldName + '_error').show(200);
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
				$('#' + this.id + '_' + fieldName + '_error').hide();
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


function autobindForms()
{
	$('form').each(function(){
		new Form($(this));
	});
}
