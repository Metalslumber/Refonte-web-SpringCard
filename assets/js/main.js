( function () {
	'use strict';

	/**
	 * Mobile nav toggle.
	 */
	var navToggle = document.getElementById( 'navtoggle' );
	var mainnav = document.getElementById( 'mainnav' );
	if ( navToggle && mainnav ) {
		navToggle.addEventListener( 'click', function () {
			var isOpen = mainnav.classList.toggle( 'open' );
			navToggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	}

	/**
	 * "À propos" dropdown: click to open/close, close on outside click or Escape.
	 */
	document.querySelectorAll( '[data-dropdown-trigger]' ).forEach( function ( trigger ) {
		var submenu = trigger.parentElement.querySelector( '.sub-menu' );
		if ( ! submenu ) {
			return;
		}
		trigger.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			var isOpen = submenu.classList.toggle( 'open' );
			trigger.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	} );

	document.addEventListener( 'click', function ( e ) {
		document.querySelectorAll( '.sub-menu.open' ).forEach( function ( submenu ) {
			if ( ! submenu.parentElement.contains( e.target ) ) {
				submenu.classList.remove( 'open' );
				var trigger = submenu.parentElement.querySelector( '[data-dropdown-trigger]' );
				if ( trigger ) {
					trigger.setAttribute( 'aria-expanded', 'false' );
				}
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			document.querySelectorAll( '.sub-menu.open' ).forEach( function ( submenu ) {
				submenu.classList.remove( 'open' );
			} );
		}
	} );

	/**
	 * Tabs (À propos: Blog / Blog technique / Contact).
	 */
	function showTab( name, btn ) {
		document.querySelectorAll( '[data-tab-panel]' ).forEach( function ( panel ) {
			panel.style.display = ( panel.getAttribute( 'data-tab-panel' ) === name ) ? 'block' : 'none';
		} );
		document.querySelectorAll( '[data-tab-trigger]' ).forEach( function ( b ) {
			b.classList.toggle( 'active', b === btn );
			b.setAttribute( 'aria-selected', b === btn ? 'true' : 'false' );
		} );
		if ( history.replaceState ) {
			history.replaceState( null, '', '#' + name );
		}
	}

	document.querySelectorAll( '[data-tab-trigger]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			showTab( btn.getAttribute( 'data-tab-trigger' ), btn );
		} );
	} );

	var tabsRoot = document.querySelector( '[data-tabs]' );
	if ( tabsRoot ) {
		var initialTab = window.location.hash ? window.location.hash.substring( 1 ) : null;
		var initialBtn = initialTab ? document.querySelector( '[data-tab-trigger="' + initialTab + '"]' ) : null;
		if ( initialBtn ) {
			showTab( initialTab, initialBtn );
		}
	}

	/**
	 * Animations d'apparition au scroll.
	 */
	if ( 'IntersectionObserver' in window ) {
		var revealObserver = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'in-view' );
						revealObserver.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
		);

		document.querySelectorAll( '.reveal:not(.in-view)' ).forEach( function ( el ) {
			revealObserver.observe( el );
		} );
	} else {
		document.querySelectorAll( '.reveal' ).forEach( function ( el ) {
			el.classList.add( 'in-view' );
		} );
	}
} )();
