(function( $, undefined ) {

	$.widget( "keatlon.form", {

		options	:
		{
			method			:	'POST',
			multipart		:	false,
			action			:	false,
			response		:	null,
			onBeforeSubmit	:	function (form) {return true;},
			onSuccess		:	function (response){},
			onError			:	function (response){}
		},

		_create	: function() {

			this.options.multipart		=	$(':file', this.element).length > 0;
			this.options.method			=	$(this.element).attr('method') ? $(this.element).attr('method') : 'POST';
			this.options.action			=	$(this.element).attr('action');

			$this						=	this;

			$(':input,:file', this.element).not('[type=submit],[type=hidden]').each(function(){

				errorSelector	=	$this._getErrorSelector($this.options.action, $this._exractFieldName($(this).attr('name')));

				if (!$('#' + errorSelector).length)
				{
					$('<div class="error"></div>').attr('id', errorSelector).insertAfter($(this));
				}
			});

			this.element.ajaxForm( {
				url         :	$this.options.action,
				type        :	$this.options.method,
				iframe      :	$this.options.multipart,
				dataType    :	'json',

				beforeSubmit: function (data, jobj, opt)
				{
					if (!$this.options.onBeforeSubmit($this.element))
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

					if ($this.options.method == 'get')
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

					$this._disableSubmit();
				},

				success: function ( response )
				{
					$this._hideErrors();

					if (typeof response != 'object')
					{
						ajax.errorHandler(response.toString());
						$this._enableSubmit();
						return;
					}

					if ( response.status == 'success')
					{
						$this.options.onSuccess(response, $this.element);
					}

					if ( response.status == 'error')
					{

						$this._showErrors(response);
						$this.options.onError(response, $this.element);
					}

					$this._enableSubmit();
				}
			} );

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