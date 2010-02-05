var dialogClass = function()
{
	var contentContainer = 'dialog';

	this.close = function ( )
	{
		$('#dialog').dialog('close');
	}

	this.load = function ( module, action, params )
	{
		ajax.get(module, action, params, function( response )
		{
			dialog.show(response.title, response.body);
			application.processAction(module, action);
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