(function($) {
    'use strict';

    $( '.ultp-color-picker' ).wpColorPicker();

    // Add target blank for upgrade button
    $('.toplevel_page_ultp-settings ul > li > a').each(function (e) {
        if($(this).attr('href').indexOf("?ultp=plugins") > 0) {
            $(this).attr('target', '_blank');
        }
    });

    if($('body').hasClass('block-editor-page')){
        $('body').addClass('ultp-editor-'+ultp_option.width);
    }

})( jQuery );