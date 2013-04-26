/**
 *	ajaxPagination
 *	turn normal HTML pagination into AJAX so only the content
 *	affected by the pagination is updated.
 * 
 *	@copyright 	2009-2013 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		22/04/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	24/04/2013
 *
 *	document.ready
 */


(function ($) {

    beancounterAjaxPagination = {


        // Run misc/generic functionality and call specific functions
        onReady: function () {
        
        	var self = this;
        	
        	
        	$('#PrimaryContent').on('click', '.pagination a', function(e){
	
				e.preventDefault();
				
				// href value of clicked link
				var URL = $(this).attr("href");
				if(URL.indexOf('?mode=ajax') == -1 && URL.indexOf('?') == -1){
					URL = URL + '?mode=ajax';
				} else if(URL.indexOf('?mode=ajax') == -1 && URL.indexOf('?') != -1){
					URL = URL + '&mode=ajax';
				} 
				
				var hash = $(this).attr("href");
				// Load in the new page
				self.loadByAjax(URL, hash);
		
			});
        	
        	
        	self.loadFromHash();
        	
        },
        
        /**
		 *	loadByAjax
		 *	Use AJAX to grab new content and load it into place
		 *	@param string URL 	/news/2009/06
		 *	@param string hash	#page2
		 */
        loadByAjax: function(URL, hash){
        
        	var self = this,
        		containerId = 'PrimaryContent',
				$container = $('#' + containerId),
				loader = '<div id="Loading">Loading new content&hellip;</div>';
			
			if($container.find('.ajax-container').length === 0){
				$container.wrapInner('<div class="ajax-container" />');
			}
		
			/* Hide current content and show loading graphic */
			$container.find('.ajax-container').addClass('invisible').end().append(loader);
			
			// Update URL - not ready yet :(
			window.location.hash = self.urlHash(URL);
		
			// Make AJAX call to set URL then if we're successful show the new content else unhide the old stuff
			var jqxhr = $.ajax({
				url: URL,
				context: $container.find('.ajax-container')
			})
			.done(function(data) { 
		
				var ajaxData = $('<div>').html(data).find('#' + containerId).html();
				$(this).html(ajaxData);
				// Re-run function to allow future AJAX calls
		  		if(typeof styliseCalendar == 'function'){
		  			styliseCalendar();
		  		}
		  		if(typeof autoSubmitOptions == 'function'){
		  			autoSubmitOptions();
		  		}
		
			})
			.fail(function() { } )
			.always(function() { 
				
				$('#Loading').remove();
				$container.find('.ajax-container').removeClass('invisible');
			
			});
			
        },
        
        /**
		 *	loadFromHash()
		 *	if a hash is already set in the URL make sure the 
		 *	correct page is loaded from it.
		 *	if not - reload the correct page via AJAX
		 */
        loadFromHash: function(){
        	var self = this,
        		currentHash = window.location.hash;
				currentHash = currentHash.replace('#','/'),
				selectedHref = $('.pagination').find('.selected').find('a').attr('href');
			
			if(currentHash.length > 0 && selectedHref.length > 0){
		
				// Does the selected href differ from the hash (to an extent)?
				if(selectedHref.indexOf(currentHash) == -1){
				
					// In the pagination the link to page 1 
					// will always be /section/ so grab it
					
					var URLPartOne = $('.pagination li:nth-child(2) a').attr('href');
					
					// Now remove all the extra slashes
					URLPartOne = URLPartOne.replace('/','');
					currentHash = currentHash.replace('/','');
					
					// Rebuild the URL and hash and then 
					// grab the required page
					var URL = '/' + URLPartOne + currentHash.replace('/', '') + '?mode=ajax';
					var hash = self.urlHash(selectedHref);
					self.loadByAjax(URL);
				}
			}
        },
        
        
        /**
		 *	urlHash()
		 *	everytime a ajax link is clicked add a hash to the URL
		 *	this functions work out what that hash should be
		 *	Allows for bookmarking and browser history
		 */
        urlHash: function(href){
        	var hash = href.replace(href.split('/')[1] + '/', '');
	
			if(hash.indexOf('?') != -1) { 
				hash = hash.split('?')[0]; 
			}
			
			return hash;
		}
	
	}


})(jQuery);


/**
 * 	document.ready (onload)
 *	initialise relevant functions
 */
$(document).ready(function(){
	beancounterAjaxPagination.onReady();
});
