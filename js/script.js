jQuery(document).ready(function($) {
    // Enable autocomplete for existing category fields.
    var autoCompleteOptionsCategory = {
        delay: 0,
        minLength: 0,
        source: function( req, response ) {
            $.getJSON('admin-ajax.php?callback=?&action=em_get_suggestions&type=category', req, response);
        },
        select: function( event, ui ) {}
    };

    $('.em_category').autocomplete(autoCompleteOptionsCategory);
    $('.em_category, .em_equipment').on( 'autocompleteselect', function ( event, ui ) {
        $( event.target ).next().val( ui.item.id );
    } );

    // Enable autocomplete for existing item fields.
    var autoCompleteOptionsEquipemts = {
        delay: 0,
        minLength: 0,
        source: function( req, response ) {
            var category = $( this.element[0] ).parent().parent().find('.em_category_id').val();
            $.getJSON('admin-ajax.php?callback=?&action=em_get_suggestions&type=equipment&category=' + category, req, response);
        },
        select: function( event, ui ) {}
    };

    $('.em_equipment').autocomplete(autoCompleteOptionsEquipemts);

    $('#add-row').on('click', function() {
        var row = $('.empty-row.screen-reader-text').clone( false );
        row.removeClass('empty-row screen-reader-text');
        row.addClass('ui-autocomplete-input');
        row.insertBefore('.empty-row.screen-reader-text');

        $( '.ui-autocomplete-input .em_category' ).autocomplete(autoCompleteOptionsCategory);
        $( '.ui-autocomplete-input .em_equipment' ).autocomplete(autoCompleteOptionsEquipemts);
        $( '.ui-autocomplete-input .em_category, .ui-autocomplete-input .em_equipment' ).on( 'autocompleteselect', function ( event, ui ) {
            $( event.target ).next().val( ui.item.id );
        } );
        return false;
    });

    $('.remove-row').on('click', function() {
        $(this).parents('tr').remove();
        return false;
    });

    $('.metabox_submit').click(function(e) {
        e.preventDefault();
        $('#publish').click();
    });
});
