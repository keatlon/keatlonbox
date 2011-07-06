(function( $, undefined ) {

	$.widget( "keatlon.form", {

		options	:
		{
			context			:	false,
			method			:	'POST',
			multipart		:	false,
			action			:	false,
			response		:	null,
			onBeforeSubmit	:	function (form) {return true;},
			onSuccess		:	function (response){},
			onError			:	function (response){}
		},

		_success			: function(response)
		{
			if (typeof response != 'object')
			{
				ajax.errorHandler(response.toString());
				this._enableSubmit();
				return;
			}

			if ( response.status == 'success')
			{
				this.options.onSuccess.apply(this.options.context, [response]);
			}

			if ( response.status == 'error')
			{
				this._showErrors(response);
				this.options.onError.apply(this.options.context, [response]);
			}

			this._enableSubmit();
		},

		_create	: function() {

			this.options.multipart		=	$(':file', this.element).length > 0;
			this.options.method			=	$(this.element).attr('method') ? $(this.element).attr('method') : 'POST';
			this.options.action			=	$(this.element).attr('action');

			self						=	this;

			$(':input,:file', this.element).not('[type=submit],[type=hidden]').each(function(){

				var	errorSelector	=	self._getErrorSelector(self.options.action, self._exractFieldName($(this).attr('name')));

				if (!$('#' + errorSelector).length)
				{
					$('<div class="error"></div>').attr('id', errorSelector).insertAfter($(this));
				}
			});

			$(this.element).ajaxForm( {
				url				:	self.options.action,
				type			:	self.options.method,
				iframe			:	self.options.multipart,
				dataType		:	'json',
				context			:	self,
				success			:	self._success,
				beforeSubmit	:	function(data, a, params)
				{
					var check = params.context.options.onBeforeSubmit.apply(params.context.options.context, [data, a, params]);

					if (!check)
					{
						return false;
					}

					/*
					* Remove default value for input text
					* */
					for(var l in data)
					{
						var obj = $('input[type=text][name="' + data[l].name + '"]');

						if (obj.length > 0)
						{
							if ( $(obj).attr('title') != '' && $(obj).attr('title') == obj.val())
							{
								data[l].value = '';
							}
						}
					}

					if (params.context.options.method == 'get')
					{
						var url = '';

						for(l in data)
						{
							url = url + '/' + data[l].name + '/' + data[l].value;
						}

						this.url = opt.url + url;

						location.href = this.url;

						return false;
					}

					params.context._disableSubmit();
					return true;
				}
			});

		},

		_disableSubmit		:	function()
		{
			$('input:submit', $(this.element)).attr('disabled', true);
			$('button:submit', $(this.element)).attr('disabled', true);
		},

		_enableSubmit		:	function()
		{
			$('input:submit', $(this.element)).attr('disabled', false);
			$('button:submit', $(this.element)).attr('disabled', false);
		},

		_showErrors			:	function(response)
		{
			this._hideErrors();
			this.options.response = response;

			if ( typeof response.errors != 'undefined' )
			{
				for ( var fieldName in response.errors )
				{
					$('#' + this._getErrorSelector(this.options.action, fieldName)).html(response.errors[fieldName]).show();
				}
			}
		},

		_hideErrors		:	function()
		{
			if ( this.options.response == null) return;

			if ( typeof this.options.response.errors != 'undefined')
			{
				for ( var fieldName in this.options.response.errors )
				{
					$('#' + this._getErrorSelector(this.options.action, fieldName)).hide();
				}
			}
		},

		_exractFieldName : function(n)
		{
			var r = new RegExp(/\[(.*)\]/g).exec(n);
			if (r == null)
			{
				return n;
			}

			return r[1];
		},

		_getErrorSelector :	function(action, fieldName)
		{
			var id = application.url2key(action)	+ '_' + this._exractFieldName(fieldName) + '_error';
			return id;
		}


	});

}(jQuery));