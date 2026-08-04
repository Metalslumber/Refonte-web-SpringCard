( function ( $ ) {
	'use strict';

	$( function () {
		$( '[data-media-picker]' ).each( function () {
			var $button = $( this );
			var target = $button.data( 'media-picker' );
			var $input = $( '#' + target + '_id' );
			var $filename = $( '#' + target + '_filename' );
			var mediaType = $button.data( 'media-type' );
			var frame;

			$button.on( 'click', function ( e ) {
				e.preventDefault();

				if ( frame ) {
					frame.open();
					return;
				}

				frame = wp.media( {
					title: $button.data( 'media-title' ),
					button: { text: $button.data( 'media-button' ) },
					library: mediaType ? { type: mediaType } : {},
					multiple: false
				} );

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					$input.val( attachment.id );
					$filename.text( attachment.filename );
				} );

				frame.open();
			} );
		} );
	} );
} )( jQuery );
