jQuery(document).ready(function($) {
    var ajaxurl             = 'admin-ajax.php';
    var ajaxaction          = 'em_get_category';
    var autoCompleteOptions = {
        delay: 0,
        minLength: 0,
        source: function( req, response ) {
            $.getJSON(ajaxurl+'?callback=?&action='+ajaxaction, req, response);
        },
        select: function( event, ui ) {}
    };

    // Enable autocomplete for existing fields.
    $('.em_category').autocomplete(autoCompleteOptions);
    $('.em_category').on( 'autocompleteselect', function ( event, ui ) {
        $( event.target ).next().val( ui.item.id );
    } );

    $('.metabox_submit').click(function(e) {
        e.preventDefault();
        $('#publish').click();
    });

    $('#add-row').on('click', function() {
        var row = $('.empty-row.screen-reader-text').clone( false );
        row.removeClass('empty-row screen-reader-text');
        row.addClass('ui-autocomplete-input');
        row.insertBefore('.empty-row.screen-reader-text');

        $( '.ui-autocomplete-input .em_category' ).autocomplete(autoCompleteOptions);
        $( '.ui-autocomplete-input .em_category' ).on( 'autocompleteselect', function ( event, ui ) {
            $( event.target ).next().val( ui.item.id );
        } );
        return false;
    });

    $('.remove-row').on('click', function() {
        $(this).parents('tr').remove();
        return false;
    });
});
