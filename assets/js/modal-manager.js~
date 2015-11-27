(function($) {
  $(document).ready(function(){
  	var readMoreElement = document.querySelectorAll(".sm-readmore-link");

	$(".sm-display-modal").fancybox({
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

	var val = $("p[data-hidden-display]").html();

	if(val == "tab") {
		$('.sm-display-modal').parent().remove();
	}
	else {
		$('.sm-display-tab').parent().remove();
	}

 });
}(jQuery));