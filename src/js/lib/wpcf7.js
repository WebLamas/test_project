$(document).ready(function(){
	$('.wpcf7').on('wpcf7mailsent',function(){
		$.fancybox.close( true );
	});
	$('.wpcf7').on('wpcf7submit',function(event){
		//console.log(event);
		//console.log('some');
		var self=this;
		window.setTimeout(function(){
		console.log($(self).find('div.wpcf7-response-output').html());
		var responseOutput = $(self).find('div.wpcf7-response-output').html();
				jQuery.fancybox.open(responseOutput);
	},100);
	});
});