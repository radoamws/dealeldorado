/**
 * DealElDorado - Plugin Frontend JavaScript
 */
(function ($) {
    'use strict';

    // Animate numbers on scroll
    function animateValue(el, start, end, duration) {
        var range = end - start;
        var current = start;
        var increment = end > start ? 1 : -1;
        var stepTime = Math.abs(Math.floor(duration / range));
        var timer = setInterval(function () {
            current += increment;
            el.textContent = current.toLocaleString('fr-FR');
            if (current === end) clearInterval(timer);
        }, stepTime);
    }

    // Comparison tabs
    $(document).on('click', '.ded-compare-tab', function () {
        var target = $(this).data('target');
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $(target).siblings().hide();
        $(target).show();
    });

})(jQuery);
