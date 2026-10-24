/* Append signed batches without replacing existing table rows or their state. */
( function () {
	'use strict';

	document.addEventListener( 'click', async function ( event ) {
		if ( ! event.target || ! event.target.closest ) {
			return;
		}
		var button = event.target.closest( '.oltew-load-more' );
		if ( ! button || button.disabled ) {
			return;
		}
		var wrapper = button.closest( '.oltew-orders' );
		var body = wrapper && wrapper.querySelector( '.oltew-table tbody' );
		if ( ! body ) {
			return;
		}
		var status = wrapper.querySelector( '.oltew-load-more-status' );
		var originalLabel = button.textContent;
		button.disabled = true;
		button.textContent = button.dataset.oltewLoading;
		wrapper.setAttribute( 'aria-busy', 'true' );
		status.textContent = '';
		try {
			var response = await fetch( button.dataset.oltewUrl, {
				method: 'POST', credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: new URLSearchParams( { action: 'oltew_load_more', nonce: button.dataset.oltewNonce, token: button.dataset.oltewToken } ).toString()
			} );
			var payload = await response.json();
			if ( ! response.ok || ! payload.success || typeof payload.data.rows !== 'string' ) {
				throw new Error( 'Invalid pagination response' );
			}
			// Elementor can replace a widget while this request is in flight.
			if ( ! button.isConnected ) {
				return;
			}
			var batch = document.createElement( 'tbody' );
			batch.innerHTML = payload.data.rows;
			Array.from( batch.children ).forEach( function ( row ) {
				if ( row.tagName === 'TR' ) {
					body.appendChild( row );
				}
			} );
			button.dataset.oltewToken = payload.data.token;
			button.dataset.oltewNonce = payload.data.nonce;
			button.hidden = ! payload.data.has_more;
			var count = wrapper.querySelector( '.oltew-count' );
			if ( count && typeof payload.data.count_label === 'string' ) {
				count.textContent = payload.data.count_label;
			}
			if ( typeof payload.data.count_label === 'string' ) {
				status.textContent = payload.data.count_label;
			}
		} catch ( error ) {
			status.textContent = button.dataset.oltewError;
		} finally {
			button.disabled = false;
			button.textContent = originalLabel;
			wrapper.setAttribute( 'aria-busy', 'false' );
		}
	} );
}() );
