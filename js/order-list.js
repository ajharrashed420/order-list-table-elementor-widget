/** Expand products and their corresponding actions together, including editor previews. */
( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		if ( ! event.target || ! event.target.closest ) {
			return;
		}
		var button = event.target.closest( '.oltew-orders .oltew-products-toggle' );
		if ( ! button ) {
			return;
		}
		var row = button.closest( '.oltew-item' );
		if ( ! row ) {
			return;
		}
		var expanded = button.getAttribute( 'aria-expanded' ) !== 'true';
		row.querySelectorAll( '[data-oltew-extra]' ).forEach( function ( item ) {
			item.hidden = ! expanded;
		} );
		button.setAttribute( 'aria-expanded', String( expanded ) );
		button.querySelector( '.oltew-expand-label' ).hidden = expanded;
		button.querySelector( '.oltew-collapse-label' ).hidden = ! expanded;
	} );
}() );
