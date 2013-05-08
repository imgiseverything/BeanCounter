/**
 *	AJAX filter
 *	use AJAX to filter results as the user is typing, selecting filter
 *	options
 * 
 *	@copyright 	2008-2013 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		27/02/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	07/05/2013
 *
 */



(function ($) {

    beancounterAjaxFilter = {
    
    	config: {
    		container: 'PrimaryContent'
    	},

        // Run misc/generic functionality and call specific functions
        onReady: function () {
        
        	var self = this;
        	self.preFilter();
        	
        },
        

        ajaxFilter: function(URL, formData){
        
        
        	var self = this,
				$container = $('#' + self.config.container),
				loader = '<div id="Loading">Loading new content&hellip;</div>';
			
			if($container.find('.ajax-container').length === 0){
				$container.wrapInner('<div class="ajax-container" />');
			}
		
			/* Hide current content and show loading graphic */
			$container.find('.ajax-container').addClass('invisible').end().append(loader);		
			
			// Make AJAX call to set URL then if we're successful show the new content else unhide the old stuff
			//console.log(formData);
		
			var jqxhr = $.ajax({
				url: URL,
				context: $container.find('.ajax-container'),
				data: formData
			})
			.done(function(data) { 
		
				var ajaxData = $('<div>').html(data).find('#' + self.config.container).html();
				$(this).html(ajaxData);
		
			})
			.always(function() { 
				
				$('#Loading').remove();
				$container.find('.ajax-container').removeClass('invisible');
			
			});
		
		
		},
        

        preFilter: function(){
        
        	var self = this,
        		searchTimeout,
        		$form = $('#filterForm'),
        		button = '<div id="FilterButton"><a href="#" id="ShowHideFilterLink">Show filter options</a></div>';

        	$('.filter-form').prepend(button);
        	
        	$('#FilterButton').click(function(e){
        		e.preventDefault();
        		$form.slideToggle('slow', function() {
					// Animation complete.
				});
        	});
        		
        		
        	$form.submit(function(e){
        		e.preventDefault();
        		self.ajaxFilter($(this).attr('action'), $(this).serialize());
        	});

			$form.find('input').bind('keyup', function() {
		        if(searchTimeout !== undefined) {
		            clearTimeout(searchTimeout);
		        }
		        searchTimeout = setTimeout(function() {
	                searchTimeout = undefined;
	                // do stuff with $this here
	                $form.submit();
		        }, 300);
			});
			
			$form.find('select').change(function(){
				$form.submit();
			});
			
			$form.find('input:checkbox').click(function(){
				$form.submit();
			});
			
		}

	}


}(jQuery));


/**
 * 	document.ready (onload)
 *	initialise relevant functions
 */
$(document).ready(function(){
	beancounterAjaxFilter.onReady();
});




