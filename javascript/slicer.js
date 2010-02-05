var slicer = function(options)
{
	var instance = this;
	
	var defaults =
	{
        name            :   'default',
		urlVarname      :	'page',
		urlEqual        :   '/',
		urlSeparator    :   '/',
		page            :   1,
		maxPage         :   1,
		possiblePage    :   1,
		baseUrl         :   location.href,
        enableKeys      :   true
	};

	this.options = $.extend(defaults, options);

	instance.options.possiblePage = instance.options.page;

	this.init = function()
	{
		if (!instance.options.page)
		{
			return false;
		}

        if (instance.options.enableKeys)
        {
            $(document).keydown(function(e)
            {
                if (e.ctrlKey)
                {
                    if (e.which == 37)
                    {
                        instance.next();
                    }

                    if (e.which == 39)
                    {
                        instance.prev();
                    }
                }
            });

            $(document).keyup(function(e)
            {
                if (e.which == 17 && instance.options.possiblePage != instance.options.page)
                {
                    instance.go();
                }
            });
        }
	}

	this.go = function()
	{
		var findStr     = instance.options.urlSeparator + instance.options.urlVarname + instance.options.urlEqual + instance.options.page;
		var replaceStr  = instance.options.urlSeparator + instance.options.urlVarname + instance.options.urlEqual + instance.options.possiblePage;

		if (instance.options.baseUrl.search(findStr) != -1)
		{
			location.href = instance.options.baseUrl.replace(findStr, replaceStr);
		}
		else
		{
			location.href = instance.options.baseUrl + replaceStr;
		}
	};

	this.next = function()
	{
		instance.options.possiblePage++;
		if (instance.options.possiblePage > instance.options.maxPage)
		{
			instance.options.possiblePage = instance.options.maxPage;
		}

		$('.page').html(instance.options.possiblePage);
	};

	this.prev = function()
	{
		instance.options.possiblePage--;

		if (instance.options.possiblePage < 1)
		{
			instance.options.possiblePage = 1;
		}
		
		$('.page').html(instance.options.possiblePage);
	};
};

var slicers = false;