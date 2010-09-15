var notificationClass = function()
{
	this.init = function()
	{
		$('body').append('<div id="notification"></div>');
		$('body').append('<div id="error"></div>');
	}

	this.success = function (message, params)
	{
		this.centered('#notification', message, 2500);
	}

	this.error = function (message, params)
	{
		this.centered('#error', message, 0);
	}

	this.centered = function(selector, message, delay)
	{
		var top = 95;
		var left = parseInt( ($('body').width() - $('#notification').width() ) / 2);

		if($.browser.msie)
		{
			top = document.documentElement.scrollTop + top;
			$(selector).css('position','absolute');
		} else
		{
			$(selector).css('position','fixed');
		}

		var top = 0;
		//var left = 0;

		$(selector).css('left', left + 'px');
		$(selector).css('top', top + 'px');


		$(selector).hide();
		$(selector).html(message);
		$(selector).fadeIn(100);
		if (delay)
		{
			nameTimer = setTimeout("$('" + selector + "').fadeOut(500)", delay);
		}
	}

	
};

var notification = new notificationClass;