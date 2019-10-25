jQuery(function($) {
    // Your code goes here.
    // Use the $ in peace...

    if ( $(".radios_type input").is(':checked') ) {

        var listing_type_value = $('.radios_type').find('input:checked').val();
        $('#sf2_select_listing').val(listing_type_value);
        $('.radios_type').find('input:checked').attr( 'checked', 'checked' );
        $('.radios_type').find('input:checked').next("label").addClass("selected");
    } else {
        var listing_type_value = $('.radios_type').find('input').filter(':first').val();
        $('#sf2_select_listing').val(listing_type_value);
        $('.radios_type').find('input').filter(':first').attr( 'checked', 'checked' );
        $('.radios_type').find('input').filter(':first').next("label").addClass("selected");

    }
    $('.radios_type input').click(function () {
        $('.radios_type input:not(:checked)').next("label").removeClass("selected");
        $('.radios_type input:checked').next("label").addClass("selected");
    });
});