var dialogClass = function()
{
	var contentContainer = 'dialog';

	this.configure = function(config)
	{
		$('body').append('<div id="dialog"></div>');
		$('#dialog').dialog(
		{
			width		:	'auto',
			autoOpen	:	false,
			bgiframe	:	true,
			modal		:	true,
			show		:	{effect:'fadeup', duration:250},
			hide		:	{effect:'fadedown', duration:250},
			minHeight	:	0,
			position	:	['center','80px'],
			closeText	:	'Close'
		});
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
		ajax.get(url, params);
	}

    this.show = function ( title, content )
	{
        $('#' + contentContainer).html( content );
        $('#ui-dialog-title-dialog').html( title );
        $('#dialog').dialog('open');
    }
};

var dialog = new dialogClass;