/*
 * Piwik Tracking jQuery functions
 */
jQuery(document).ready(function($) {
    $('.ui-tabs').tabs({
        fx : {
            opacity : 'toggle',
            duration : 80
        },
        show : onSelect,
        cookie : {}
    });
    function onSelect(event, ui) {
        $('.ui-tabs-nav li a').removeClass('nav-tab-active');
        $('.ui-tabs-selected a').addClass('nav-tab-active');
    }

});
