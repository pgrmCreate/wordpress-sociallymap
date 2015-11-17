(function($) {
  $(document).ready(function(){
  	var readMoreElement = document.querySelectorAll(".sm-readmore-link");
  	
	$(".sm-readmore-link").fancybox({
		maxWidth	: 1400,
		maxHeight	: 600,
		fitToView	: false,
		width		: '90%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
 });
}(jQuery));