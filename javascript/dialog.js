var dialogClass = function()
{
	this.options =
	{
		element		:	false,
		framework	:	'ui',
		width		:	'auto',
		height		:	'auto',
		autoOpen	:	false,
		autoResize	:	true,
		bgiframe	:	true,
		modal		:	true,
		show		:	{effect:'fadeup', duration:250},
		hide		:	{effect:'fadedown', duration:250},
		resizable	:	false,
		id			:	'dialog',
		minHeight	:	0,
		position	:	['center', 80],
		closeText	:	'Close',
		close		:	function ()
		{
			$('.wrapper').removeClass('blurred');
		},
		open		:	function ()
		{
			$('.wrapper').addClass('blurred');
		}
	};

	this.configure = function(config)
	{
		this.options.framework	=	(typeof $.fn.dialog == 'function') ? 'ui' : 'bootstrap';

		if (this.options.framework == 'ui')
		{
			this.options.element	=	$('<div>').attr('id', this.options.id);
			$('body').append(this.options.element);
			this.options.element.dialog(dialog.options);
		}

		if (this.options.framework == 'bootstrap')
		{
			this.options.element	=	$('#' + this.options.id);
		}

	}

	this.close = function ( )
	{
		if (this.options.framework == 'ui')
		{
			this.options.element.dialog('close');
		}

		if (this.options.framework == 'bootstrap')
		{
			this.options.element.hide();
		}
	}

	this.process = function ( response )
	{
		if (!response.body)
		{
			return false;
		}

		if (this.options.framework == 'ui')
		{
			if (typeof response.options != 'undefined')
			{
				this.options.element.dialog('option', response.options);
			}
		}

		dialog.show(response.title, response.body);

		response.application.js.selectors.push({
			'selector'	:	'#' + this.options.id,
			'init'		:	2
		});


		if (response.position)
		{
			if (this.options.framework == 'bootstrap')
			{
				$(this.options.element).position(response.position);
			}
		}
		else
		{
			if (this.options.framework == 'bootstrap')
			{
				$(this.options.element).position({
					at 			: 	'center',
					my			:	'center',
					of			:	'body',
					offset		:	'0 -150'
				});
			}
		}

	}

	this.load = function ( url, params, cb )
	{
		ajax.get(url, params, cb, 'dialog');
	}

    this.show = function ( title, content )
	{
		if (this.options.framework == 'ui')
		{
			$(this.options.element).html( content );
			$('#ui-dialog-title-dialog').html( title );
	        $(this.options.element).dialog('open');
		}


		if (this.options.framework == 'bootstrap')
		{
			this.options.element.show();

			$('.modal-header h3', this.options.element).html(title);
			$('.modal-body', this.options.element).html(content);
		}
    }
};

var dialog = new dialogClass;