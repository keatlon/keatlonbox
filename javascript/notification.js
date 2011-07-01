var notificationClass = function()
{
	this.timer	=	false;

	this.options =
	{
		parent			:	'body',

		selector		:	'.notifications',

		itemClass		:	'notification',

		position		:	'center',
		
		show			:	function()
		{
			// $(notification.options.selector).hide();
			// $(notification.options.selector).fadeIn(100);
		},

		hide			:	function()
		{

			// $(notification.options.selector + ':visible').fadeOut(500);
		},
		
		delay			:	2500
	}

	this.configure = function(options)
	{
		notification.options		=	$.extend(notification.options, options);

		$('body').append($('<div>').addClass('notifications').css({
			position	:	'fixed'
		}));

		var parentPosition	=	$(notification.options.parent).position();

		var left	=	parentPosition.left;

		switch(notification.options.position)
		{
			case 'right':
				$(notification.options.selector).css('bottom', '');
				$(notification.options.selector).css('left', '');
				$(notification.options.selector).css('right', '0px');
				$(notification.options.selector).css('top', '40px');
				break;

			case 'center':
				left	= parseInt( ($('body').width() - $('#' + notification.options.id).width() ) / 2);

				$(notification.options.selector).css('bottom', '');
				$(notification.options.selector).css('right', '');
				$(notification.options.selector).css('left', left + 'px');
				$(notification.options.selector).css('top', '0px');

				break;

			case 'bottom':
				
				$(notification.options.selector).css('bottom', '40px');
				$(notification.options.selector).css('right', '');
				$(notification.options.selector).css('left', left + 'px');
				$(notification.options.selector).css('top', '');

				break;

		}
	}

	this.success = function (message, params)
	{
		this.show(message);
	}

	this.error = function (message, params)
	{
		this.show(message);
	}

	this.show = function(message)
	{
		this.push(message);
	}

	this.process = function(response)
	{
		
	}

	this.push = function(message)
	{
		var item = $('<div>').addClass(notification.options.itemClass).html(message).css( {marginLeft : -2500} );

		$(notification.options.selector).append(item);

		$(item).animate(
			{
				marginLeft: 0
			},
			400
		);

		if (notification.options.delay)
		{
			this.timer = setTimeout(function(){
			$(item).fadeOut(300);
			}, notification.options.delay);
		}
	}

};

var notification = new notificationClass;