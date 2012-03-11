var dialogClass = function()
{
	var contentContainer = 'dialog';
	this.options =
	{
		width		:	'auto',
		height		:	'auto',
		autoOpen	:	false,
		autoResize	:	true,
		bgiframe	:	true,
		modal		:	true,
		show		:	{effect:'fadeup', duration:250},
		hide		:	{effect:'fadedown', duration:250},
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
		$('body').append('<div id="dialog"></div>');
		$('#dialog').dialog(dialog.options);
	}

	this.close = function ( )
	{
		$('#dialog').dialog('close');
	}

	this.process = function ( response )
	{
		if (!response.body)
		{
			return false;
		}

		if (typeof response.options != 'undefined')
		{
			$('#dialog').dialog('option', response.options);
		}

		dialog.show(response.title, response.body);

		response.application.js.selectors.push({
			'selector'	:	'#' + contentContainer,
			'init'		:	2
		});
	}

	this.load = function ( url, params )
	{
		ajax.get(url, params, false, 'dialog');
	}

    this.show = function ( title, content )
	{
        $('#' + contentContainer).html( content );

        $('#ui-dialog-title-dialog').html( title );
        $('#dialog').dialog('open');
    }
};

var dialog = new dialogClass;