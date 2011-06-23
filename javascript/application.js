var applicationClass = function ()
{
	this.config			=	{}
	this.response		=	{};
	
	this.isPageLoading		=	false;
	this.loadedJavaScript	=	{};

	this.configure = function(config)
	{
		this.config = config;
		ajax.configure(config.ajax);
		facebook.configure(config.facebook);
		dialog.configure(config.dialog);
		notification.configure(config.notification);
		comet.configure(config.comet);
	}

	this.dispatch = function(response)
	{
		application.response	=	response;

		for (var l in response.js.commands)
		{
			switch(response.js.commands[l].command)
			{
				case 'init':
					eval ("$('" + response.js.commands[l].selector + "')." + response.js.commands[l].plugin + "(response.js.commands[l].params);");
					break;

				case 'set':
					$(response.js.commands[l].selector).html(response.js.commands[l].html);
					break;

				case 'remove':
					$(response.js.commands[l].selector).remove();
					break;

			}
		}

		for( var c in response.js.contexts)
		{
			this.initUi(response.js.contexts[c]);
		}

		// 
	}

	this.initUi = function(parent)
	{
		if (typeof parent == 'undefined' || !parent)
		{
			return false;
		}

		parent	=	$(parent);

		this.initForms(parent);
		this.initSlicers(parent);
		this.initUrl(parent);

		if (typeof this.init != 'undefined')
		{
			this.init(parent);
		}

		$('[data-plugin]', parent).each(function(){
			eval ("$(this, parent )." + $(this).data('plugin') + "();");
			$(this, parent ).data();
		});

		$('input[title],textarea[title]', parent).hint();
		
		$('.elastic', parent).elastic();
		
		$('.focused', parent).eq(0).focus();

		$(".tooltip", parent).tooltip({
			position	:	"top center",
			effect		:	'slide',
			delay		:	200
		});


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

		$('form[action]', parent).form();
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
				options[a]	=	element.attr('data-' + a);
			}
		}

		return options;
	}

};

var application = new applicationClass();
