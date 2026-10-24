/* Remote product/category selectors using Elementor's native Select2 control. */
( function ( $ ) {
	'use strict';
	var registered = false;

	function registerCatalogControl() {
		if ( registered ) {
			return;
		}
		registered = true;
		var Base = elementor.modules.controls.Select2;
		elementor.addControlView( 'oltew_catalog_select', Base.extend( {
			getControlValue: function () {
				var value = Base.prototype.getControlValue.apply( this, arguments );
				return Array.isArray( value ) ? value : String( value || '' ).split( ',' ).map( function ( part ) { return part.trim(); } ).filter( Boolean );
			},
			getSelect2DefaultOptions: function () {
				var view = this;
				return $.extend( Base.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
					placeholder: oltewCatalog.placeholder,
					ajax: {
						url: oltewCatalog.url,
						type: 'POST',
						dataType: 'json',
						delay: 250,
						data: function ( params ) {
							return { action: 'oltew_catalog_search', nonce: oltewCatalog.nonce, catalog_type: view.model.get( 'catalog_type' ), q: params.term || '', page: params.page || 1 };
						},
						processResults: function ( response ) {
							return response && response.success ? response.data : { results: [] };
						}
					}
				} );
			},
			applySavedValue: function () {
				var view = this;
				var values = this.getControlValue() || [];
				if ( ! Array.isArray( values ) ) {
					values = String( values ).split( ',' ).filter( Boolean );
				}
				values.forEach( function ( value ) {
					var exists = view.ui.select.find( 'option' ).filter( function () { return String( this.value ) === String( value ); } ).length;
					if ( ! exists ) {
						view.ui.select.append( new Option( String( value ), String( value ), true, true ) );
					}
				} );
				Base.prototype.applySavedValue.apply( this, arguments );
				if ( ! values.length ) {
					return;
				}
				$.ajax( { url: oltewCatalog.url, type: 'POST', dataType: 'json', data: { action: 'oltew_catalog_search', nonce: oltewCatalog.nonce, catalog_type: this.model.get( 'catalog_type' ), ids: values } } ).done( function ( response ) {
					if ( view.isDestroyed === true || ! response || ! response.success ) {
						return;
					}
					response.data.results.forEach( function ( result ) {
						view.ui.select.find( 'option' ).filter( function () { return String( this.value ) === String( result.id ); } ).text( result.text );
					} );
					view.ui.select.trigger( 'change.select2' );
				} );
			}
		} ) );
	}

	$( window ).on( 'elementor:init', registerCatalogControl );
	if ( window.elementor && elementor.modules && elementor.modules.controls ) {
		registerCatalogControl();
	}
}( jQuery ) );
