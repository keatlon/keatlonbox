(function($) {

    jQuery.fn.counter = function(options)
    {
          var defaults = {};

          var options   = jQuery.extend(defaults, options);

          var capacityCalculator = function(e)
          {
              var l = jQuery(e.target).val().length;
              jQuery('#' + options.label).html(l);

              if (typeof options.cb != 'undefined')
              {
                  options.cb(l);
              }

              return true;
          };

          return this.each(function() {
            jQuery(this).bind('change', capacityCalculator).
                bind('keyup', capacityCalculator).
                bind('blur', capacityCalculator).
                bind('focus', capacityCalculator).
                bind('input', capacityCalculator).
                bind('paste', capacityCalculator);
          });

    };
	
})(jQuery);