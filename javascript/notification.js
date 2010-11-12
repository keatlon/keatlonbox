var notificationClass = function()
{
	this.options =
	{
		id				:	'notification',

		className		:	'notification',

		position		:	'center',
		
		show			:	function()
		{
			$('#' + notification.options.id).hide();
			$('#' + notification.options.id).fadeIn(100);
		},

		hide			:	function(){
			$('#' + notification.options.id).fadeOut(500);
		},
		
		delay			:	2500
	}

	this.init = function()
	{
		$('body').append($('<div>').attr('id', notification.options.id).css({
			position	:	'fixed',
			display		:	'none'
		}).addClass(notification.options.className));
	}

	this.success = function (message, params)
	{
		this.show(message);
	}

	this.error = function (message, params)
	{
		this.show(message);
	}

	this.applyPosition = function()
	{
		var left	=	0;

		switch(notification.options.position)
		{
			case 'right':
				$('#' + notification.options.id).css('bottom', '');
				$('#' + notification.options.id).css('left', '');
				$('#' + notification.options.id).css('right', '0px');
				$('#' + notification.options.id).css('top', '40px');
				break;
				
			case 'center':
				left	= parseInt( ($('body').width() - $('#' + notification.options.id).width() ) / 2);

				$('#' + notification.options.id).css('bottom', '');
				$('#' + notification.options.id).css('right', '');
				$('#' + notification.options.id).css('left', left + 'px');
				$('#' + notification.options.id).css('top', '0px');
				
				break;
		}

		return {'left'  : left, 'top' : top};
	}

	this.show = function(message)
	{
		$('#' + notification.options.id).html(message);
		
		notification.applyPosition();

		this.options.show();

		if (notification.options.delay)
		{
			nameTimer = setTimeout("notification.options.hide()", notification.options.delay);
		}
	}

	
};

var notification = new notificationClass;