var LoggerClass = function ()
{
	this.readMemcacheKey = function( _key, id )
	{
		$.post('/index.php/debug/readCache', {key:_key}, function (response) {
			$("#mckey_"+id).html(response.data.value);
			$("#mckey_"+id).show();
		}, 'json');
	}

	this.AjaxError = function( event, request, settings )
	{
		dialogType = 'static';
		$("#dialog").dialog({title:'Ajax Error', position:[20, 50], open:loadDialog, closeOnEscape:true, width:850, height:'auto', top:50, modal:true, overlay:{backgroundColor:'#444', opacity:'0.0'}});
		$("#dialog_static_content").html(request.responseText);
	}
	
	this.AjaxResponse = function( event, request, settings )
	{
		if ( typeof ( request ) == 'undefined')
		{
			return;
		}
		
		var s = '';
		for ( var i in settings )
		{
			s += i + ': ' + settings[i] + '; ';
		}
		
		try
		{
			data = eval("(" + request.responseText + ")");
			
			if ( typeof ( data.data.ignoreUpdate ) != 'undefined')
			{
				return;
			}
			
			$("#cache_details").prepend(data.debug.memcache);
		}
		catch ( e )
		{}
	}
	
	this.varDump = function ( theObj, offset )
	{
		var dump = '';
		
		if ( typeof offset == 'undefined' )
		{
			offset = 0;
		}
	
		if ( theObj.constructor == Array || theObj.constructor == Object )
		{
			for ( var p in theObj )
			{
				for ( var i = 0; i < offset; i++ )
				{
					dump += '&nbsp;&nbsp;';
				}
				
      			if ( ( typeof theObj[p] == 'array' ) || ( typeof theObj[p] == 'object') )
      			{
      				dump += p + ' [' + typeof(theObj[p]) + ']: ' + "\n" + this.varDump(theObj[p], offset + 1);
				}
				else
				{
					dump += p + ': ' +  theObj[p] + ' (' + typeof theObj[p] + ')';
					dump += "\n";
				}
    		}
		}
		else
		{
			dump += theObj + "\n";
		}
		
		
		return dump;
	}
};

var Logger = new LoggerClass();