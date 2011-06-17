var notificationClass = function()
{
	this.timer	=	false;

	this.options =
	{
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

	this.init = function()
	{
		$('body').append($('<div>').addClass('notifications').css({
			position	:	'fixed'
		}));

		var left	=	0;

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
		var item = $('<div>').addClass(notification.options.itemClass).html(message);

		$(notification.options.selector).append(item);


		$(item).animate(
			{
				top	: 60
			},
			200,
			'easeInExpo',
			function(){
			}
		);

		if (notification.options.delay)
		{
			this.timer = setTimeout(function(){


				$(item).animate(
					{
						marginLeft	: 1200
					},
					400,
					'easeInExpo',
					function(){
						$(this).remove();
					}
				);

			}, notification.options.delay);
		}
	}

};

var notification = new notificationClass;