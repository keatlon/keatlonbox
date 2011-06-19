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
			minHeight	:	0,
			closeText	:	'Close'
		});
	}

	this.close = function ( )
	{
		$('#dialog').dialog('close');
	}

	this.load = function ( url, params )
	{
		ajax.get(url, params, function( response )
		{
			dialog.show(response.title, response.body);
			application.dispatch(response, $('#' + contentContainer));
		})
	}

	this.url = function ( url, params )
	{
		ajax.url(url, params, function( response )
		{
			dialog.show(response.title, response.body);
			application.dispatch(response, $('#' + contentContainer));
		})
	}

    this.show = function ( title, content )
	{
        $('#' + contentContainer).html( content );
        $('#ui-dialog-title-dialog').html( title );
        $('#dialog').dialog('open');
    }
};

var dialog = new dialogClass;