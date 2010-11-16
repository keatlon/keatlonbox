var dialogClass = function()
{
	var contentContainer = 'dialog';

	this.init = function()
	{
		$('body').append('<div id="dialog"></div>');
		$('#dialog').dialog({
			width		:	450,
			autoOpen	:	false,
			bgiframe	:	true,
			modal		:	true,
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
			boot_page(response);
		})
	}

	this.url = function ( url, params )
	{
		ajax.url(url, params, function( response )
		{
			dialog.show(response.title, response.body);
			boot_page(response);
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