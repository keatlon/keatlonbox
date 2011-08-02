var applicationClass = function ()
{
	this.config			=	{};
	this.response		=	{};
	this.vars			=	{};
	
	this.isPageLoading		=	false;
	this.loadedJavaScript	=	{};

	this.configure = function(config)
	{
		this.config = config;

		if (typeof ajax != 'undefined')
		{
			ajax.configure(config.ajax);
		}

		if (typeof dialog != 'undefined')
		{
			dialog.configure(config.dialog);
		}

		if (typeof notification != 'undefined')
		{
			notification.configure(config.notification);
		}

		if (typeof facebook != 'undefined')
		{
			facebook.configure(config.facebook);
		}

		if (typeof comet != 'undefined')
		{
			comet.configure(config.comet);
		}

	}

	this.dispatch = function(response)
	{
		this.vars		=	$.extend(this.vars, response.vars);
		this.response	=	response;

		for (var l in response.application.js.commands)
		{
			switch(response.application.js.commands[l].command)
			{
				case 'init':
					eval ("$('" + response.application.js.commands[l].selector + "')." + response.application.js.commands[l].plugin + "(response.application.js.commands[l].params);");
					break;

				case 'set':
					$(response.application.js.commands[l].selector).html(response.application.js.commands[l].html);
					break;

				case 'append':
					$(response.application.js.commands[l].selector).append(response.application.js.commands[l].html);
					break;

				case 'prepend':
					$(response.application.js.commands[l].selector).prepend(response.application.js.commands[l].html);
					break;

				case 'replace':
					$(response.application.js.commands[l].selector).replaceWith(response.application.js.commands[l].html);
					break;

				case 'remove':
					$(response.application.js.commands[l].selector).remove();
					break;

				case 'attr':
					$(response.application.js.commands[l].selector).attr(response.application.js.commands[l].attr, response.application.js.commands[l].value);
					break;

				case 'raw':
					eval (response.application.js.commands[l].value + ";");
					break;

				case 'animate':
					eval (response.application.js.commands[l].method + "($('" + response.application.js.commands[l].selector + "'));");
					break;

			}
		}

		for( var c in response.application.js.contexts)
		{
			this.initUi(response.application.js.contexts[c].context, response.application.js.contexts[c].init);
		}

		eval( "if ( typeof " + response.application.js.dispatcher + " == 'function' ) { " + response.application.js.dispatcher + "(response) }; " );
	}

	this.getElements = function(selector, context, init)
	{
		if (init == 1)
		{
			return $(context);
		}
		
		if (init == 2)
		{
			return $(selector, $(context));
		}
	}

	this.initUi = function(context, init)
	{
		this.initForms(context, init);

		this.initSlicers(context, init);
		this.initUrl(context, init);

		this.getElements('[data-plugin]', context, init).each(function(){

			if ($(this).data('plugin'))
			{
				eval ("$(this)." + $(this).data('plugin') + "();");
			}
		});

		this.getElements('input[title],textarea[title]', context, init).hint();

		this.getElements('.focused', context, init).eq(0).focus();

		if( typeof $.tooltip != 'undefined')
		{

			this.getElements('.tooltip', context, init).tooltip({
				position	:	"top center",
				effect		:	'slide',
				delay		:	200
			});
		}

		this.getElements('.elastic', context, init).elastic();
	}

	this.initUrl	=	function(context, init)
	{
		this.getElements('a:not(.sf-usual),input[type="button"]', context, init).click(function() {

			if ($(this).attr('target') == 'dialog')
			{
				dialog.url($(this).attr('href'));
				return false;
			}

			if ($(this).attr('target') == 'post')
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

	this.initForms = function(context, init)
	{
		this.getElements('form[action]', context, init).form();
	}

	this.initSlicers = function(context, init)
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

	this.url2key = function (url)
	{
		return this.url2method(url);
	}

	this.url2method = function (url)
	{
		var parts	=	url.substring(1).split('/');
		var method	=	'';

		for (var l in parts)
		{
			if (l == 0)
			{
				method = parts[l];
				continue;
			}

			method = method + parts[l].substring(0, 1).toUpperCase() + parts[l].substring(1, parts[l].length);
		}

		return method;
	}

	this.options = function (element)
	{
		var options	=	{};

		for (var a in element.data())
		{
			if (element.attr('data-' + a))
			{
				options[a]	=	element.data(a);
			}
		}

		return options;
	}

	this.getVar	=	function(name)
	{
		return this.vars[name];
	}

};

var application = new applicationClass();
