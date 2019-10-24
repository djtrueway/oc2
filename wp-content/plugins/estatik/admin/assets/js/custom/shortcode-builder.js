( function( $ ) {
    'use strict';

    $( function() {

        $( 'body' ).magnificPopup( {
            delegate: '.js-es-shortcode-builder__link',
            type: 'ajax'
        } );

        $( document ).on( 'change', '.js-shortcode-field', function() {
            var $resultContainer = $( this ).closest( '#shortcode-builder-popup' ).find( '.js-shortcode-content' );

            $resultContainer.html( Estatik.tr.loading_shortcode_params );

            $.get( Estatik.ajaxurl, { nonce: $( this ).data('nonce'), action: 'es_shortcode_builder_params', shortcode: $( this ).val()  }, function( response ) {
                $resultContainer.html( response );

                $( '.js-select2-multiple' ).select2( {
                    tags: true,
                    multiple: true,
                    dropdownParent: $( '#shortcode-builder-popup' ),
                } ).val('').trigger('change');

                $( '.js-select2' ).select2( {
                    dropdownParent: $( '#shortcode-builder-popup' )
                } );

                $( '.js-select2-properties' ).select2( {
                    tags: true,
                    ajax: {
                        url: Estatik.ajaxurl + '?action=es_select2_search_properties&nonce=' + Estatik.settings.admin_nonce,
                        dataType: 'json',
                        minimumInputLength: 3,
                        delay: 500
                    },
                    dropdownParent: $( '#shortcode-builder-popup' )
                } );

                $( '[data-tooltipster-content]' ).each( function() {
                    var content = $( this ).data( 'tooltipster-content' );
                    $( this ).tooltipster({
                        contentAsHTML: true,
                        theme: 'tooltipster-borderless',
                        side: ['right'],
                        debug: false
                    }).tooltipster( 'content', content );
                } );
            } );

            if ( $( this ).val() ) {
                $( '.js-insert-shortcode' ).removeClass( 'hidden' );
            } else {
                $( '.js-insert-shortcode' ).addClass( 'hidden' );
            }
        } );

        $( document ).on( 'submit', '.js-es-shortcode-builder-form', function() {
            var editor_id = $( this ).data( 'editor' );
            var $btn = $( '.js-insert-shortcode' );
            var label = $btn.val();

            $btn.val( Estatik.tr.btn_generating );
            // $btn.prop( 'disabled', 'disabled' );

            $.get( Estatik.ajaxurl, $( this ).serialize(), function( response ) {

                if ( ! tinymce.get( editor_id ) ) {
                    $( '#' + editor_id ).val( response ).trigger( 'change' );
                } else {
                    tinymce.get( editor_id ).execCommand('mceInsertContent', false, response );
                }

                $.magnificPopup.close();
            } ).always( function() {
                // $btn.removeProp( 'disabled' );
                $btn.val( label );
            } );

            return false;
        } );
    } );

    $( document ).on( 'click', '.js-es-sb-close', function() {
        $.magnificPopup.close();
    } );
} )( jQuery );
