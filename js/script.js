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
	$('.em_category, .em_equipment').on( 'autocompleteselect', autocompleteSelectAction );

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

	$( '.em_equipment' ).autocomplete(autoCompleteOptionsEquipemts);
	var previous;
	$( '.em_qty' ).on( 'focus', function() {
		previous = this.value;
	} ).change( qty_validation );

	$('#add-row').on('click', function() {
		var row = $('.empty-row.screen-reader-text').clone( false );
		row.removeClass('empty-row screen-reader-text');
		row.addClass('ui-autocomplete-input');
		row.insertBefore('.empty-row.screen-reader-text');

		$( '.ui-autocomplete-input .em_category' ).autocomplete(autoCompleteOptionsCategory);
		$( '.ui-autocomplete-input .em_equipment' ).autocomplete(autoCompleteOptionsEquipemts);
		$( '.ui-autocomplete-input .em_category, .ui-autocomplete-input .em_equipment' ).on( 'autocompleteselect', autocompleteSelectAction );
		$( '.ui-autocomplete-input .em_qty' ).on( 'focus', function() {
			previous = this.value;
		} ).change( qty_validation );
		return false;
	});

	function autocompleteSelectAction( event, ui ) {
		$( event.target ).next().val( ui.item.id );
		$( event.target ).attr( 'data-avail-qty', ui.item.avail_qty );
	}

	function qty_validation() {
		var avail_qty = parseInt ( $( this ).parent().parent().find('.em_equipment').attr('data-avail-qty') );
		if ( parseInt ( $( this ).val() ) > avail_qty ) {
			alert('Sorry! Total available quantity is ' + avail_qty );
			$( this ).val( previous );
			$( this ).focus();
		} else {
			previous = $( this ).val();
		}
	}

	$('.remove-row').on('click', function() {
		$( this ).parents('tr').remove();
		return false;
	});

	$('.metabox_submit').click(function(e) {
		e.preventDefault();
		$('#publish').click();
	});
});
