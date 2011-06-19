var applicationClass = function ()
{
	this.config	=
	{
		ajax			:	{},
		facebook		:	{},
		comet			:	{},
		dialog			:	{},
		form			:	{},
		notification	:
		{
			position	:	'bottom'
		}

	};

	this.context =
	{
		module: null,
		action: null
	};
	
	this.isPageLoading		=	false;
	this.loadedJavaScript	=	{};

	this.run = function(config)
	{
		this.context.module = config.context.module;
		this.context.action = config.context.action;

		if (typeof application.configure != 'undefined')
		{
			application.configure();
		}
		
		ajax.init(this.config.ajax);
		facebook.init(this.config.facebook);
		dialog.init(this.config.dialog);
		notification.init(this.config.notification);
		comet.init(this.config.comet);
		
		this.dispatch({
			context	:	application.context
		});
	}

	this.dispatch = function(response, parent)
	{
		if (typeof response != 'undefined' && response)
		{
			application.context	=	response.context;
			this.processAction(this.context.module, this.context.action, response);
		}

		this.initUi(parent);
	}

	this.addContext = function( variables )
	{
		for ( var name in variables )
		{
			this.context[name] = variables[name];
		}
	}
	
	this.initUi = function(parent)
	{
		if (typeof parent == 'undefined')
		{
			parent	=	$('body');
		}

		this.initForms(parent);
		this.initSlicers(parent);
		this.initUrl(parent);

		if (typeof this.init != 'undefined')
		{
			this.init(parent);
		}

	}

	this.initUrl	=	function(parent)
	{
		$('a:not(.sf-usual)', $(parent)).click(function() {

			if ($(this).attr('target') == 'dialog')
			{
				dialog.url($(this).attr('href'));
				return false;
			}

			if ($(this).attr('target') == 'put')
			{
				ajax.put($(this).attr('href'));
				return false;
			}

			if ($(this).attr('href') == '#' || $(this).attr('href') == 'javascript:;')
			{
				return false;
			}

			if (application.enableAjaxNavigation)
			{
				$.address.value($(this).attr('href'));
				return false;
			}
		});
	}

	this.initForms = function(parent)
	{
		if (typeof parent == 'undefined')
		{
			parent	=	$('body');
		}

		$('form[action]', parent).each(function()
		{
			new Form($(this));
		});
	}

	this.initSlicers = function(parent)
	{
		for(l in slicers)
		{
			slicers[l].obj = new slicer( slicers[l]);
			slicers[l].obj.init();
		}
	}

	function initAjaxNavigation()
	{

		if (application.enableAjaxNavigation)
		{
			if (window.location.pathname != '/')
			{
				application.backAjaxNavigation = true;
			}
		}
		else
		{
			return;
		}

		$.address.change(function(event) {

			application.log('$.address.change value: ' + event.value);

			if (event.value == '~')
			{
				application.isPageLoading = false;
				application.log('$.address.change ignoring hash');
				return true;
			}

			if (window.location.pathname != '/' && application.isPageLoading)
			{
				application.isPageLoading = false;
				application.log('$.address.change ignoring page loading');
				return true;
			}

			if (application.backAjaxNavigation)
			{
				application.log('$.address.change back ajax');
				location.href= '/#' + event.value;
				return false;
			}

			$.get(event.value, {}, function (response){
				$('#content').hide();
				$('#content').html(response.body);
				$('#content').fadeIn(300);

				boot_page(response);
			}, 'json');

			application.isPageLoading = false;
		});
	}


	this.processAction = function( module, action, response )
	{
		action = action.substring(0, 1).toUpperCase() + action.substring(1, action.length);
		eval( "if ( typeof " + module + action + " == 'function' ) { " + module + action + "(response) }; " );
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
		if ( application.loadedJavaScript[js] )
		{
			return;
		}
		
		application.loadedJavaScript[js] = true;
		$.getScript( js, callback );
	}

	this.log = function (message)
	{
		$('#jsdebug').append('<div class="sf-debug-item">' + message + '</div>');
	}
	
};

var application = new applicationClass();
