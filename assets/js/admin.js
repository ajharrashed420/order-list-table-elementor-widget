/* Enhance native, accessible settings inputs with WordPress color pickers. */
( function ( $ ) {
	'use strict';
	$( function () {
		$( '.oltew-admin .oltew-color' ).wpColorPicker();
		var links = Array.from( document.querySelectorAll( '.oltew-admin-nav .oltew-menu-item' ) );
		var scheduled = false;
		function highlightSection() {
			var active = links[ 0 ];
			links.forEach( function ( link ) {
				var section = document.getElementById( link.hash.slice( 1 ) );
				if ( section && section.getBoundingClientRect().top <= 100 ) {
					active = link;
				}
			} );
			links.forEach( function ( link ) {
				link.classList.toggle( 'is-active', link === active );
				if ( link === active ) {
					link.setAttribute( 'aria-current', 'location' );
				} else {
					link.removeAttribute( 'aria-current' );
				}
			} );
			scheduled = false;
		}
		highlightSection();
		window.addEventListener( 'scroll', function () {
			if ( ! scheduled ) {
				scheduled = true;
				window.requestAnimationFrame( highlightSection );
			}
		}, { passive: true } );
	} );
}( jQuery ) );
