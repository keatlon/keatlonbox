var applicationClass = function ()
{
	this.context = {
		module: null,
		action: null
	};
	
	var _loadedJavaScript = {};

	this.execute = function()
	{
		this.init();
		this.processAction(this.context.module, this.context.action);
	}
	
	this.addContext = function( variables )
	{
		for ( var name in variables )
		{
			this.context[name] = variables[name];
		}
	}
	
	this.init = function()
	{
		this.context.module = app.context.module;
		this.context.action = app.context.action;
	}
	
	this.processAction = function( module, action )
	{
		action = action.substring(0, 1).toUpperCase() + action.substring(1, action.length);
		eval( "if ( typeof " + module + action + " == 'function' ) { " + module + action + "() }; " );
	}
	
	this.url = function( module, action, params )
	{
		var url_params = '';
		
		if (typeof params == 'object')
		{
			for (var param_name in params)
			{
				url_params = url_params + '/' + param_name + '/' + params[param_name];
			}
		}

		if ( typeof action == 'undefined' )
		{
			return '/' + module + url_params;
		}
	
		return '/' + module + '/' + action  + url_params;
	}
	
	this.redirect = function( module, action )
	{
		document.location = this.url( module, action );
	}
	
	this.addJavaScript = function( js, callback )
	{
		if ( _loadedJavaScript[js] )
		{
			return;
		}
		
		_loadedJavaScript[js] = true;
		$.getScript( js, callback );
	}

	this.log = function (message)
	{
		$('#jsdebug').append('<div class="sf-debug-item">' + message + '</div>');
	}
	
};

var application = new applicationClass();
