(function($) {
    $(document).ready(function(){
        var saveStyle = $('html').css('overflow');

        $(document).on('click touchstart', '.sm-readmore-link', function(e) {
            if ($(document).width() < 600) {
                return true;
            }


            var self = $(this);

            var href = self.attr('href');

            href = googleUrlVerification(href);

            $('html').css('overflow', 'hidden');


            var iframe = '<div class="sm-iframe-wrapper"><iframe class="modalw-iframe" src="'+href+'" id="modalw-sm-iframe"></iframe></div>';
            var wrapper = '<div class="modalw-wrapper"><div class="modalw-load-wrapper"><p class="modalw-sm-loader"></div></p></div>';

            $('body').append(wrapper+'<div class="modalw-container"><p class="modalw-close">X</p>'+iframe+'</div>');

            $('#modalw-sm-iframe').load(function() {
                $('.modalw-container').css('visibility', 'visible');
                $('.modalw-load-wrapper').remove();

                resizingWindow();

                $(window).resize(function() {
                    resizingWindow();
                });
            });

            return false;
        });

        $(document).on('click touchstart', '.modalw-close, .modalw-wrapper', function (e) {
            $('.modalw-container').fadeOut('slow', function() {
                $(this).remove();
            });
            $('.modalw-wrapper').fadeOut('slow', function() {
                $(this).remove();
            });
            $('html').css('overflow', saveStyle);

            return false;
        });

        function resizingWindow() {
            // Get size of windows
            var widthWindows = $(document).width();

            // Calc size of modal (80% of windows)
            var widthDynamic = widthWindows * 0.8;

            // Set size of modal
            $('.modalw-container').width(widthDynamic);

            // calc margin left
            var marginPicked = (widthWindows * 0.1)/1.3;

            // set margin left to windows
            $('.modalw-container').css('left', marginPicked+'px');
        }

        function googleUrlVerification(url) {
            var addressTargeting = 'https://www.google.com/url';
            var isUnlessGoogle = url.indexOf(addressTargeting) > -1;
            var realUrl = '';
            var MAX_SEARCHER = 150;

            if (isUnlessGoogle) {
                var currentIndex = url.indexOf('url=') + 4;
                var endUrl = true;
                var controllerLoop = 0;
                var currentChar = '';
                while(endUrl) {
                    currentChar = url.substr(currentIndex, 1);
                    if(currentChar !== '&') {
                        realUrl += currentChar;
                    } else {
                        endUrl = false;
                    }

                    controllerLoop++;
                    currentIndex++;

                    if(controllerLoop === MAX_SEARCHER) {
                        endUrl = false;
                    }
                }

                return realUrl;
            } else {
                return url;
            }
        }
    });
}(jQuery));
