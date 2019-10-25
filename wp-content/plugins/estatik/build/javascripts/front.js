( function( $ ) {
    'use strict';

    var priority = Estatik.widgets.search.initPriority;

    /**
     * Append select field options.
     *
     * @param items
     * @param $el
     */
    function appendOptions( items, $el ) {
        items = JSON.parse(items);

        $el.html('');

        if (items) {
            var label = $el.find('option[value=0]').html();
            label = label ? label : Estatik.tr.select_location;
            $el.html('<option value="">' + label + '</option>');
            for (var i in items) {
                if ( ! items[i].long_name ) continue;
                $el.append('<option value="' + items[i].id + '">' + items[i].long_name + '</option>');
            }
            $el.removeProp('disabled');
        }
    }

    /**
     * Load location items.
     *
     * @param object
     * @param $el
     */
    function loadItems( object, $el ) {
        object.action = 'es_get_location_items';
        object.nonce = Estatik.settings.front_nonce;
        $.post(Estatik.ajaxurl, object, function(response) {
            appendOptions(response, $el);
            var val = $el.data('value');
            if (val) {
                $el.val(val);
                $el.trigger('change');
            }
        });
    }

    /**
     * Initialize base field location.
     *
     * @param priority
     */
    function initBaseLocation( priority ) {
        if ( priority ) {
            for ( var i in priority ) {
                var $initField = $('[data-type=' + i + ']');
                if ( $initField.length ) {
                    loadItems( {type: i, status: 'initialize'}, $initField );
                    break;
                }
            }
        }
    }

    $( function() {
        var $searchWrap = $( '.es-search__wrapper' );
        var $requestForm = $( '.es-request-widget-wrap form' );
        var $responseBlock = $( '.es-response-block' );
        var $select2Tags = $( '.es-select2-tags' );
        var $select2Base = $( '.js-es-select2-base' );

        initBaseLocation(priority);

        $(document).on('change', '.js-es-location', function() {
            var $el = $(this);
            var type = $el.data('type');
            var val = $el.val();

            if (priority[type]) {

                for (var i in priority[type]) {
                    if ( typeof priority[type][i] == 'string' ) {
                        var $depEl = $('[data-type=' + priority[type][i] + ']');
                        if ($depEl) {
                            loadItems({type: priority[type][i], status: 'dependency', val: val}, $depEl);
                        }
                    }
                }
            }
        });

        $( '.js-es-tabs' ).each( function() {
            var $links_container = $( this ).find( '.js-es-tabs__links' );
            var $content_container = $( this ).find( '.js-es-tabs__content' );

            $links_container.find( 'a' ).on( 'click touch tap', function() {
                var $container = $( $( this ).attr( 'href' ) );
                $links_container.find( 'li' ).removeClass( 'active' );
                $( this ).closest( 'li' ).addClass( 'active' );
                $content_container.find( '.js-es-tabs__tab' ).removeClass( 'active' );
                $container.addClass( 'active' );
                $( window ).trigger( 'resize' );
                // window.location.hash = $( this ).attr( 'href' );

                return false;
            } );

            var hash = window.location.hash;

            if ( hash && $content_container.find( hash ).length ) {
                $links_container.find( 'a[href=' + hash + ']' ).trigger( 'click' ).trigger( 'touch' );
            } else if ( ! $content_container.find( '.js-es-tabs__tab.active' ).length ) {
                $links_container.find( 'li:first-child a' ).trigger( 'click' ).trigger( 'touch' );
            }
        } );

        if ( $select2Tags.length ) {
            $select2Tags.select2( {
                tags: true
            } );
        }

        if ( $select2Base.length ) {
            $select2Base.select2();
        }

        $searchWrap.find( 'select:not(.es-select2-tags)' ).select2();

        $requestForm.on('submit', function() {

            var formData = $( this ).serialize();
            var $form = $( this );

            $.post( Estatik.ajaxurl, formData, function( response ) {
                response = JSON.parse( response );
                $responseBlock.html( response );
                $form.hide();
            } );

            return false;
        });

        $( document ).on( 'click', '.js-es-request-form-show', function() {
            $requestForm.show();

            $responseBlock.html('');
            $requestForm.find( 'input[type=text], input[type=tel], input[type=email], textarea' ).val( '' );

            if (typeof grecaptcha !== 'undefined') {
                if ( Estatik.settings.recaptcha_version === 'v2' ) {
                    grecaptcha.reset();
                }
            }
        });

        $searchWrap.find('input[type=reset]' ).click( function() {
            var $form = $( this ).closest( 'form' );

            $form.find( '.es-search__field' ).find( 'input, select' )
                .val( null )
                .attr( 'value', '' )
                .attr( 'data-value', '' )
                .trigger( 'change' );

            var $select2Tags = $form.find( '.es-select2-tags' );

            if ( $select2Tags.length ) {
                $select2Tags.select2( 'val', '' );
                $select2Tags.select2( 'data', null );
                $select2Tags.find( 'option' ).removeProp( 'selected' ).removeAttr( 'selected' );
            }
        } );

        // Upload ling on register page.
        $( '.js-trigger-upload' ).click( function() {
            $( $( this ).data('selector') ).trigger( 'click' );

            return false;
        } );

        // Input on register page.
        $( '.js-es-input-image' ).change( function() {
            var el = this;
            var reader = new FileReader();

            reader.onload = function( e ) {
                $( el ).closest( 'div' ).find( '.js-es-image' ).html( "<img src='" + e.target.result + "'>" );
            };

            reader.readAsDataURL( el.files[0] );

            $( '.js-trigger-upload span' ).html( Estatik.tr.replace_photo );
        } );

        $( '.js-autocomplete-wrap input' ).keyup( function() {
            var $el = $( this );
            var action = $( this ).data( 'action' );

            $.post( Estatik.ajaxurl, {
                action: action,
                nonce: Estatik.settings.front_nonce,
                s: $( this ).val()
            }, function( response ) {
                $el.closest( '.js-autocomplete-wrap' ).find( '.es-autocomplete-result' ).html( response );
            } );
        } );

        $( '.js-autocomplete-wrap' ).on( 'click', 'li', function( e ) {
            var $el = $( this );
            var $parent = $el.closest( '.js-autocomplete-wrap' );
            $parent.find( 'input' ).val( $el.data( 'content' ) );
            $parent.find( '.es-autocomplete-result' ).html('');
            e.stopPropagation();
        } );

        $( 'body' ).click(function(){
            $( '.es-autocomplete-result' ).html( '' );
        });

        $( '.es-recaptcha-wrapper .g-recaptcha' ).each( function() {
            if ( ! $(this).closest('.contact-block__send-form-wrapper').length ) {
                var recaptcha = $(this);
                var newScaleFactor = recaptcha.parent().innerWidth() / 304;
                recaptcha.css('transform', 'scale(' + newScaleFactor + ')');
                recaptcha.css('transform-origin', '0 0');
                setTimeout( function() {
                    recaptcha.parent().height(recaptcha[0].getBoundingClientRect().height);
                }, 600 );
            }
        } );

        $( document ).on( 'click', '.js-es-wishlist-button', function() {

            var $link = $( this );
            $link.removeClass( 'error' );
            if ( ! $link.hasClass( 'preload' ) ) {
                var data = $link.data();
                data.action = 'es_wishlist_' + data.method;
                data.nonce = Estatik.settings.wishlist_nonce;
                $link.addClass( 'preload' );
                var $container = $link.closest( '#es-saved-homes' );
                var $item = $link.closest( 'li.properties' );

                $.post( Estatik.ajaxurl, data, function( response ) {

                    response = response || {};

                    if ( response.status === 'success' ) {
                        $( $link ).replaceWith( response.data );

                        if ( $container.length ) {
                            $item.fadeOut( 400, function() {
                                $item.remove();
                            } );
                        }
                    } else {
                        $link.addClass( 'error' );
                        if ( response.message ) {
                            alert( response.message );
                        }
                    }
                }, 'json' ).always( function() {
                    $link.removeClass( 'preload' );
                } ).fail( function() {
                    $link.addClass( 'error' );
                } );
            }

            return false;
        } );

        $( '.js-es-save-search' ).click( function() {
            var $btn = $( this );
            var $form = $btn.closest( 'form' );

            var label = $btn.val();

            $btn.val( Estatik.tr.saving );

            var data = new FormData( $form[0] );
            data.append( 'action', 'es_save_search' );
            data.append( 'nonce', Estatik.settings.save_search_nonce );

            if ( ! $btn.hasClass( 'es-button-green-corner' ) ) {
                $btn.addClass( 'es-button-green-corner' );
            }

            $form.find( '.js-es-search__messages' ).html('');

            $.ajax( {
                url: Estatik.ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function( response ) {
                    response = response || {};

                    if ( response.status === 'success' ) {
                        $btn.val( Estatik.tr.saved );
                    } else if ( response.status === 'error' ) {
                        $btn.val( Estatik.tr.error );
                        $btn.removeClass( 'es-button-green-corner' ).addClass( 'es-button-error' );
                    }

                    if ( response.message ) {
                        $form.find( '.js-es-search__messages' ).html( response.message );
                    }
                }
            } ).always( function() {

            } ).fail( function() {
                $btn.val( Estatik.tr.error );
                $btn.removeClass( 'es-button-green-corner' ).addClass( 'es-button-error' );
            } );
        } );

        $( '.js-es-change-update-method' ).change( function() {

            var $el = $( this );
            var $msg_container = $el.closest( 'form' ).find( '.es-msg-container' );

            $msg_container.html( '' );

            var data = {
                action: 'es_change_update_method',
                nonce: Estatik.settings.save_search_change_method_nonce,
                id: $( this ).data( 'id' ),
                update_method: $( this ).val()
            };

            $.post( Estatik.ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.message ) {

                    if ( response.status === 'success' ) {
                        response.message = '<p class="es-message es-message-success"><i class="fa fa-check-circle-o" aria-hidden="true"></i> ' + response.message +'</p>';
                    } else {
                        response.message = '<p class="es-message es-message-error"><i class="fa fa-times-circle-o" aria-hidden="true"></i> ' + response.message +'</p>';
                    }
                    $msg_container.html( response.message );
                }
            }, 'json' ).fail( function() {
                alert( Estatik.tr.system_error );
            } );
        } );

        $( '.js-es-login-form' ).click( function() {

            $.get( Estatik.ajaxurl, { action: 'es_login_form', nonce: Estatik.settings.front_nonce }, function( response ) {

                $.magnificPopup.open( {
                    items: {
                        src: response,
                        type: 'inline'
                    }
                } );

            } ).fail( function() {
                alert( Estatik.tr.system_error );
            } );

            return false;
        } );

        var $profile_wrapper = $( '.es-profile__wrapper--horizontal' );
        var $profile_nav = $( '.es-profile__wrapper--horizontal .es-profile__tabs-wrapper' );

        $( window ).on( 'resize', function() {
            var nav_width = 0;

            $profile_nav.find( 'li' ).each( function() {
                nav_width += $( this ).outerWidth();
            } );

            if ( $profile_nav.find( 'ul' ).hasClass( 'slick-initialized' ) ) {
                $profile_nav.find( 'ul' ).slick( 'unslick' );
            }

            if ( $profile_wrapper.width() < nav_width ) {
                $profile_nav.find( 'ul' ).slick( {
                    variableWidth: true,
                    prevArrow: '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    nextArrow: '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                } );
            }
        } ).trigger( 'resize' );

        $( '.js-switch-block' ).click( function() {
            var $container = $( $( this ).data( 'block' ) );

            if ( $container.hasClass( 'hidden' ) ) {
                $container.removeClass( 'hidden' );
            } else {
                $container.addClass( 'hidden' );
            }

            return false;
        } );

        $( '.js-saved-search-save' ).click( function() {
            var $container = $( this ).closest( '.es-saved-search__item' );
            var $msg_container = $container.find( '.es-msg-container' );

            $.post( Estatik.ajaxurl, $( this ).closest( 'form' ).serialize(), function( response ) {
                response = response || {};

                if ( response.message ) {

                    if ( response.status === 'success' ) {
                        response.message = '<p class="es-message es-message-success"><i class="fa fa-check-circle-o" aria-hidden="true"></i> ' + response.message +'</p>';
                        $container.find( '.js-saved-search-title' ).html( response.title );
                    } else {
                        response.message = '<p class="es-message es-message-error"><i class="fa fa-times-circle-o" aria-hidden="true"></i> ' + response.message +'</p>';
                    }
                    $msg_container.html( response.message );
                }
            }, 'json' ).fail( function() {
                alert( Estatik.tr.system_error );
            } ).always( function() {
                $container.find( '.js-switch-block' ).trigger( 'click' );
            } );

            return false;
        } );

        var $properties_slideshow = $( '.js-es-slideshow' );

        if ( $properties_slideshow.length ) {
            $properties_slideshow.each( function() {

                var $el = $( this );
                var numSlides = $el.children().length;
                var item = $el.data( 'args' );
                var slidesToShow = parseInt(item.slides_to_show) || 1;

                slidesToShow = slidesToShow >= numSlides && item.slider_effect === 'vertical' ?
                    numSlides : slidesToShow;

                var responsive = [];

                if ( slidesToShow > 3 ) {
                    responsive.push( {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            // infinite: true,
                            dots: true
                        }
                    } );
                }

                if ( slidesToShow > 2 ) {
                    responsive.push( {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    } );
                }

                responsive.push( {
                    breakpoint: 200,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                } );

                var settings = {
                    margin: 20,
                    slidesToShow: slidesToShow,
                    arrows: 1 == item.show_arrows || false,
                    prevArrow: '<span class="es-slick-arrow es-slick-prev"></span>',
                    nextArrow: '<span class="es-slick-arrow es-slick-next"></span>',
                    responsive: responsive
                };

                if ( ! settings.arrows ) {
                    settings.autoplay = true;
                }

                if ( item.slider_effect === 'vertical' ) {
                    settings.vertical = true;
                    settings.verticalSwiping = true;
                    // settings.infinite = false;
                    settings.autoplaySpeed = 5000;
                }

                $el.slick( settings );
            } );
        }

        $( '.es-slide__bottom' ).each(function() {
            var $this = $(this);

            if ( $this.find( '.es-bottom-icon' ).length === 2 ) {
                $this.find( '.es-bottom-icon:last-child' ).css( { 'text-align': 'right' } );
            }
        } );
    } );
})( jQuery );
