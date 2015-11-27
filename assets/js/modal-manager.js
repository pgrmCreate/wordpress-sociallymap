(function($) {
  $(document).ready(function(){
	if ($('.sm-readmore-link').attr('data-display-type') == "modal") {
		$('.sm-readmore-link').removeAttr('target');
		$(".sm-display-modal").fancybox({
			maxWidth	: 1900,
			maxHeight	: 1200,
			fitToView	: false,
			width		: '90%',
			height		: '80%',
			autoSize	: false,
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none'
		});
		} else {
			$('.sm-readmore-link').removeAttr('data-fancybox-type');
		}
	});
	

 });
}(jQuery));
