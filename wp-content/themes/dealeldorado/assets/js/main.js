/**
 * DealElDorado - Main JavaScript
 */
(function ($) {
    'use strict';

    // =========================================================
    // Search Autocomplete
    // =========================================================
    var searchTimeout;
    var $input = $('#ded-search-input');
    var $suggestions = $('#ded-search-suggestions');

    if ($input.length) {
        $input.on('input', function () {
            clearTimeout(searchTimeout);
            var query = $(this).val().trim();

            if (query.length < 2) {
                $suggestions.removeClass('active').empty();
                return;
            }

            searchTimeout = setTimeout(function () {
                $.post(ded_vars.ajax_url, {
                    action: 'ded_search_suggestions',
                    nonce: ded_vars.nonce,
                    query: query
                }, function (response) {
                    if (response.success && response.data.length) {
                        var html = '';
                        $.each(response.data, function (i, item) {
                            html += '<a href="' + item.url + '" class="ded-suggestion-item">' +
                                '<i class="fas fa-search"></i>' +
                                '<span>' + item.title + '</span>' +
                                '</a>';
                        });
                        $suggestions.html(html).addClass('active');
                    } else {
                        $suggestions.removeClass('active').empty();
                    }
                });
            }, 300);
        });

        // Close on click outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.ded-search-wrapper').length) {
                $suggestions.removeClass('active');
            }
        });

        // Keyboard navigation
        $input.on('keydown', function (e) {
            var $items = $suggestions.find('.ded-suggestion-item');
            var $active = $items.filter('.active');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if ($active.length && $active.next().length) {
                    $active.removeClass('active').next().addClass('active');
                } else {
                    $items.first().addClass('active');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if ($active.length && $active.prev().length) {
                    $active.removeClass('active').prev().addClass('active');
                }
            } else if (e.key === 'Enter' && $active.length) {
                e.preventDefault();
                window.location.href = $active.attr('href');
            } else if (e.key === 'Escape') {
                $suggestions.removeClass('active');
            }
        });
    }

    // =========================================================
    // Search Results: Grid / List Toggle
    // =========================================================
    $('#view-grid').on('click', function () {
        $(this).addClass('active').siblings().removeClass('active');
        $('#results-container').removeClass('ded-list-view').addClass('row-cols-sm-2');
        $('.result-item').removeClass('col-12').addClass('col-sm-6 col-lg-6');
    });

    $('#view-list').on('click', function () {
        $(this).addClass('active').siblings().removeClass('active');
        $('#results-container').addClass('ded-list-view');
        $('.result-item').removeClass('col-sm-6 col-lg-6').addClass('col-12');
    });

    // =========================================================
    // Sticky Header Shadow
    // =========================================================
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 10) {
            $('.ded-header').addClass('ded-header-scrolled');
        } else {
            $('.ded-header').removeClass('ded-header-scrolled');
        }
    });

    // =========================================================
    // Smooth Scroll for Anchor Links
    // =========================================================
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: target.offset().top - 80 }, 400);
        }
    });

    // =========================================================
    // Price Alert Form (simple)
    // =========================================================
    $(document).on('submit', '.ded-alert-form', function (e) {
        e.preventDefault();
        var email = $(this).find('input[type=email]').val();
        if (!email) return;

        $.post(ded_vars.ajax_url, {
            action: 'ded_price_alert',
            nonce: ded_vars.nonce,
            email: email,
            post_id: $(this).data('post-id')
        }, function (response) {
            if (response.success) {
                alert('Alerte créée avec succès !');
            }
        });
    });

    // =========================================================
    // Animate on scroll (simple IntersectionObserver)
    // =========================================================
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('ded-animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.ded-cat-card, .ded-product-card, .ded-blog-card').forEach(function (el) {
            observer.observe(el);
        });
    }

    // =========================================================
    // Tooltip initialization
    // =========================================================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

})(jQuery);
