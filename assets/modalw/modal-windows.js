(function($) {
    $(document).ready(function(){
        $('a.sm-readmore-link').click(function(target) {
            var attr = $(this).attr('open-connect');
            var href = $(this).attr('href');
            target.preventDefault();
            if (typeof attr !== typeof undefined && attr !== false) {
                var saveStyle = $('html').css('overflow');
                $('html').css('overflow', 'hidden');
                var iframe = '<iframe class="modalw-iframe" src="'+href+'" id="modalw-sm-iframe"></iframe>';
                var wrapper = '<div class="modalw-wrapper"><div class="modalw-load-wrapper"><p class="modalw-sm-loader"></div></p></div>';
                $('body').append(wrapper+'<div class="modalw-container"><p class="modalw-close">X</p>'+iframe+'</div>');
                $('#modalw-sm-iframe').load(function(){
                    console.log("oui");
                    $('.modalw-container').css('display', 'block');
                    $('.modalw-load-wrapper').remove();
                    resizingWindow();
                    $( window ).resize(function() {
                        resizingWindow();
                    });
                });
            }

            $('.modalw-close, .modalw-wrapper').click(function () {
                $('.modalw-container').fadeOut("slow", function() {
                    $(this).remove();
                });
                $('.modalw-wrapper').fadeOut("slow", function() {
                    $(this).remove();
                });
                $('html').css('overflow', saveStyle);
            });

            function resizingWindow() {
                var widthWindows = $(document).width();
                var widthDynamic = widthWindows * 0.8;
                $('.modalw-container').width(widthDynamic);
                var marginPicked = (widthWindows * 0.1)/1.3;
                $('.modalw-container').css('left', marginPicked+"px");
            }
        });
    });
}(jQuery));
