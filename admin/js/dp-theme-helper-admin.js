(function ($) {
    'use strict';

    var $window = $(window);

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    function scrollToHash() {
        var $input = $(location.hash);

        if ($input[0]){
            var $inputBlock = $input.parents('tr'),
                scrollPoint = $input.offset().top - $window.height() / 2;
            $('html, body').animate({
                scrollTop: scrollPoint > 0 ? scrollPoint : 0
            }, {
                duration: 1000,
                complete: function () {
                    $inputBlock.addClass('highlight');
                }
            });
        } else {

        }
    }

    $window.ready(function () {
        if (location.hash) {
            scrollToHash();
        }
        $window.on('hashchange', function () {
            scrollToHash();
        })
    })

})(jQuery);
