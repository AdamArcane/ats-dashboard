/**
 * Notice Bell.
 *
 * Relocates the admin notices the server collected (see
 * Notice_Bell_Module::render_holder()) from their hidden holder into the
 * notice bell's dropdown panel in the toolbar, keeps the badge count in
 * sync, and drops notices out of the count when the visitor dismisses them.
 *
 * @package ATS_Dashboard
 */

( function () {
	'use strict';

	function hasContent( node ) {
		return !! node.querySelector( 'img, svg, a[href], input, select, textarea, button:not(.notice-dismiss), .button' )
			|| node.textContent.trim() !== '';
	}

	function updateBadge( badge, bellNode, count ) {
		if ( ! badge ) {
			return;
		}

		if ( count > 0 ) {
			badge.textContent = count > 99 ? '99+' : String( count );
			badge.hidden = false;

			if ( bellNode ) {
				bellNode.classList.add( 'has-notices' );
			}
		} else {
			badge.hidden = true;

			if ( bellNode ) {
				bellNode.classList.remove( 'has-notices' );
			}
		}
	}

	function renderEmptyState( panelList, emptyText ) {
		var empty = document.createElement( 'p' );
		empty.className = 'ats-notice-bell-empty';
		empty.textContent = emptyText;
		panelList.appendChild( empty );
	}

	function init() {
		var settings   = window.atsNoticeBell || {};
		var holderId   = settings.holderId || 'ats-notice-bell-holder';
		var i18n       = settings.i18n || {};
		var panelList  = document.getElementById( 'ats-notice-bell-list' );
		var badge      = document.getElementById( 'ats-notice-bell-badge' );
		var bellNode   = document.getElementById( 'wp-admin-bar-ats-notice-bell' );

		if ( ! panelList ) {
			return;
		}

		var holder = document.getElementById( holderId );
		var count  = 0;

		if ( holder ) {
			Array.prototype.slice.call( holder.children ).forEach( function ( notice ) {
				if ( ! hasContent( notice ) ) {
					return;
				}

				notice.classList.add( 'ats-collected-notice' );
				panelList.appendChild( notice );
				count++;
			} );

			holder.parentNode.removeChild( holder );
		}

		updateBadge( badge, bellNode, count );

		if ( 0 === count ) {
			renderEmptyState( panelList, i18n.empty || 'No notifications' );
		}

		// WordPress removes a dismissed notice's element from the DOM itself;
		// watch for that so the badge count (and the empty state) stay accurate.
		if ( 'MutationObserver' in window ) {
			var observer = new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( mutation ) {
					Array.prototype.forEach.call( mutation.removedNodes, function ( node ) {
						if ( node.nodeType !== 1 || ! node.classList || ! node.classList.contains( 'ats-collected-notice' ) ) {
							return;
						}

						count = Math.max( 0, count - 1 );
						updateBadge( badge, bellNode, count );

						if ( 0 === count && ! panelList.querySelector( '.ats-notice-bell-empty' ) ) {
							renderEmptyState( panelList, i18n.empty || 'No notifications' );
						}
					} );
				} );
			} );

			observer.observe( panelList, { childList: true } );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
