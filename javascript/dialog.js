var dialogSelector = function()
{
	var dialogSelector = '#dialog';
	this.options =
	{
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
			$('body').append('<div id="dialog"></div>');
			$('#dialog').dialog(dialog.options);
		}
	}

	this.close = function ( )
	{
		if (this.options.framework == 'ui')
		{
			$('#dialog').dialog('close');
		}

		if (this.options.framework == 'bootstrap')
		{
			$(dialogSelector).hide();
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
					$('#dialog').dialog('option', response.options);
			}

			dialog.show(response.title, response.body);
		}

		if (this.options.framework == 'bootstrap')
		{
			$(dialogSelector).show();
		}

		response.application.js.selectors.push({
			'selector'	:	dialogSelector,
			'init'		:	2
		});
	}

	this.load = function ( url, params, cb )
	{
		ajax.get(url, params, cb, 'dialog');
	}

    this.show = function ( title, content )
	{
		if (this.options.framework == 'ui')
		{
			$(dialogSelector).html( content );
			$('#ui-dialog-title-dialog').html( title );
	        $('#dialog').dialog('open');
		}

		if (this.options.framework == 'boostrap')
		{
			content;
			title;
		}
    }
};

var dialog = new dialogSelector;