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

    $(document).on('click', '.ultp-addons-enable', function(e){
        const that = this
        $.ajax({
            url: ultp_option.ajax,
            type: 'POST',
            data: {
                action: 'ultp_addon', 
                addon: $(that).data('addon'),
                value: this.checked,
                wpnonce: ultp_option.security
            },
            success: function(data) {
                if( $(that).data('addon') == 'ultp_templates' ) {
                    location.reload();   
                }
            },
            error: function(xhr) {
                console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
            },
        });
    });

    const actionBtn = $('.page-title-action');
    const savedBtn = $(".ultp-saved-templates-action");
    if ( savedBtn.length > 0 ) {
        if(savedBtn.data())
        actionBtn.addClass('ultp-save-templates-pro').text( savedBtn.data('text') )
        actionBtn.attr( 'href', savedBtn.data('link') )
        actionBtn.attr( 'target', '_blank' )
    }

})( jQuery );