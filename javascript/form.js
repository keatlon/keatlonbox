(function( $, undefined ) {

	$.widget( "keatlon.form", {

		options	:
		{
			context			:	false,
			method			:	'POST',
			multipart		:	false,
			action			:	false,
			response		:	null,
			onSubmit		:	function (form) {return true;},
			onSuccess		:	function (response){},
			onError			:	function (response){}
		},

		_prepareData	: function(data) 
		{
			for(var fieldName in data)
			{
				var obj = $('input[type=text][name="' + fieldName + '"]');

				if (obj.length > 0)
				{
					if ( $(obj).attr('title') != '' && $(obj).attr('title') == obj.val())
					{
						data[fieldName] = '';
					}
				}
			}
			
			return data;
		},

		_createIFrame	: function() 
		{
			var self				=	this;

			self.element.append(
				$('<input type="hidden">').
				attr("name", "KBOX_REQUEST_SRC").
				attr("value", "iframe")
			);

			self.options.iframeId	=	this._getIFrameSelector(this.options.action);
			self.options.iframe		=	$('<iframe>').
			attr("id", self.options.iframeId).
			attr('name', self.options.iframeId).
			css({ position: 'absolute', top: '-1000px', left: '-1000px' });

			self.element.attr('target', self.options.iframeId);
			$('body').append(self.options.iframe);

			self.options.iframe.bind('load', function (){
				var response	=	$.parseJSON(self.options.iframe.contents().find('textarea').val());

				if (!response)
				{
					return false;
				}

				application.dispatch(response);
				self._onResponse(response);
			});

		},

		_markup	: function() {
			
			var self					=	this;
			
			$(':input,:file', this.element).not('[type=submit],[type=hidden],.ignore').each(function(){

				var	errorSelector	=	self._getErrorSelector(self.options.action, self._exractFieldName($(this).attr('name')));

				if (!$('#' + errorSelector).length)
				{
					$('<div class="error"></div>').attr('id', errorSelector).insertAfter($(this));
				}
			});
			
			self._createIFrame();
		},

		_create	: function() {

			var self				=	this;
			
			this.options.multipart	=	$(':file', this.element).length > 0;
			this.options.method		=	$(this.element).attr('method') ? $(this.element).attr('method') : 'POST';
			this.options.action		=	$(this.element).attr('action');

			if (this.options.context)
			{
				if (this.options.onSuccess)
				{
					this.options.onSuccess	=	$.proxy(this.options.onSuccess, this.options.context);
				}

				if (this.options.onError)
				{
					this.options.onError	=	$.proxy(this.options.onError, this.options.context);
				}

				if (this.options.onSubmit)
				{
					this.options.onSubmit	=	$.proxy(this.options.onSubmit, this.options.context);
				}
			}

			this._markup();

			self.element.bind('submit', function (event)
			{
				
				if (self.options.onSubmit)
				{
					if (!self.options.onSubmit())
					{
						return false;
					}
				}

				self._disableSubmit();
			});
			
		},

		_onResponse	:	function (response)
		{
			this._enableSubmit();
			
			this._hideErrors(response);
			this._showErrors(response);
			
			if (typeof response != 'object')
			{
				return	ajax.errorHandler(response.toString());
			}
			
			if (response.status == "success" && this.options.onSuccess)
			{
				this.options.onSuccess(response);
			}
			
			if (response.status != "success" && this.options.onError)
			{
				this.options.onError(response);
			}
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

			this._hideErrors();

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
		},
		
		_getIFrameSelector : function(action)
		{
			return application.url2key(action)	+ '_iframe';
		}

	});

}(jQuery));


(function($,undefined){
  '$:nomunge'; // Used by YUI compressor.
  
  $.fn.serializeObject = function(){
    var obj = {};
    
    $.each( this.serializeArray(), function(i,o){
      var n = o.name,
        v = o.value;
        
        obj[n] = obj[n] === undefined ? v
          : $.isArray( obj[n] ) ? obj[n].concat( v )
          : [ obj[n], v ];
    });
    
    return obj;
  };
  
})(jQuery);