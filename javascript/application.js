var applicationClass = function ()
{

	this.RENDERER_BASE      =	0;
	this.RENDERER_HTML      =	1;
	this.RENDERER_JSON      =	2;
	this.RENDERER_XML		=	3;
	this.RENDERER_DIALOG    =	4;

	this.config			=	{};
	this.response		=	{};
	this.vars			=	{};
	this.contexts		=	{};
	
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

		if (typeof comet != 'undefined')
		{
			comet.configure(config.comet);
		}

	}

	this.getGlobalContext	=	function()
	{
		return this.contexts.global;
	}

	this.getContext	=	function()
	{
		return this.contexts.current;
	}


	this.dispatch = function(response)
	{
		this.vars		=	$.extend(this.vars, response.vars);
		this.response	=	response;

		if(typeof response.redirect != 'undefined')
		{
			location.href = response.redirect;
		}

		if(typeof response.jsonredirect != 'undefined')
		{
			location.href = response.jsonredirect;
		}

		if (response.notice)
		{
			notification.success(response.notice);
		}

		if (response.warning)
		{
			notification.warning(response.warning);
		}

		if (response.status == 'exception')
		{
			console.log('Exception', response.errors);
		}

		if (response.status == 'error')
		{
			console.log('Error', response.errors);
		}

		switch(response.application.renderer)
		{
			case	this.RENDERER_HTML:
				this.contexts.global	=	response.application.module + response.application.action;
				break;

			case	this.RENDERER_JSON:
				this.contexts.current	=	response.application.module + response.application.action;
				break;

			case	this.RENDERER_DIALOG:
				this.contexts.current	=	response.application.module + response.application.action;
				dialog.process(response);
				break;

		}

		for (var l in response.application.js.commands)
		{
			switch(response.application.js.commands[l].command)
			{
				case 'init':
					eval ("$('" + response.application.js.commands[l].selector + "')." + response.application.js.commands[l].plugin + "(response.application.js.commands[l].params);");
					break;

				case 'html':
					$(response.application.js.commands[l].selector).html(response.application.js.commands[l].html);
					break;

				case 'append':
					$(response.application.js.commands[l].selector).append(response.application.js.commands[l].html);
					break;

				case 'prepend':
					$(response.application.js.commands[l].selector).prepend(response.application.js.commands[l].html);
					break;

				case 'replaceWith':
					$(response.application.js.commands[l].selector).replaceWith(response.application.js.commands[l].html);
					break;

				case 'remove':
					$(response.application.js.commands[l].selector).remove();
					break;

				case 'hide':
					$(response.application.js.commands[l].selector).hide();
					break;

				case 'show':
					$(response.application.js.commands[l].selector).show();
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

		for( var c in response.application.js.selectors)
		{
			this.initUi(response.application.js.selectors[c].selector, response.application.js.selectors[c].init);
		}

		eval( "if ( typeof " + response.application.js.dispatcher + " == 'function' ) { " + response.application.js.dispatcher + "(response) }; " );
	}

	this.getElements = function(selector, parentSelector, init)
	{
		if (init == 1)
		{
			return $(parentSelector);
		}
		
		if (init == 2)
		{
			return $(selector, $(parentSelector));
		}
	}

	this.initUi = function(selector, init)
	{
		this.initForms(selector, init);
		this.initSlicers(selector, init);
		this.initUrl(selector, init);

		this.getElements('[data-plugin]', selector, init).each(function(){

			if ($(this).data('plugin'))
			{
				eval ("$(this)." + $(this).data('plugin') + "();");
			}
		});

		if (typeof $.fn.hint != 'undefined')
		{
			this.getElements('input[title],textarea[title]', selector, init).hint();
		}

		this.getElements('.focused', selector, init).eq(0).focus();

		if(typeof $.fn.tooltip != 'undefined')
		{
			this.getElements('[tooltip]', selector, init).tooltip({
				position	:	"top center",
				effect		:	'slide',
				opacity		:	0.8,
				delay		:	200
			});
		}

		if (typeof $.fn.elastic != 'undefined')
		{
			this.getElements('.elastic', selector, init).elastic();
		}
		
		if (typeof $.fn.tabby != 'undefined')
		{
			this.getElements('.tabby', selector, init).tabby();
		}
		
	}

	this.initUrl	=	function(selector, init)
	{
		this.getElements('[href]:not(.sf-usual)', selector, init).click(function() {

			if (typeof $(this).attr('repeat') != 'undefined')
			{
				var r = parseInt($(this).attr('repeat'));

				if (r > 0)
				{
					$(this).addClass('repeat');
					$(this).attr('repeat', r - 1);
					return false;
				}
			}

			if ($(this).attr('target') == 'post')
			{
				ajax.put($(this).attr('href'));
				return false;
			}

			if ($(this).attr('target') == 'get' || $(this).attr('target') == 'dialog')
			{
				ajax.get($(this).attr('href'));
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

		this.getElements('input[type="button"][href]', selector, init).click(function ()
		{
			if ($(this).attr('target') == 'post')
			{
				ajax.put($(this).attr('href'));
				return false;
			}

			dialog.load($(this).attr('href'));
		});

	}

	this.initForms = function(selector, init)
	{
		this.getElements('form[action]', selector, init).form();
	}

	this.initSlicers = function(selector, init)
	{
		if (typeof slicers == 'undefined')
		{
			return false;
		}

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
			if (a == "plugin")
			{
				continue;
			}
			
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
