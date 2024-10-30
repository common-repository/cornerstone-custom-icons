jQuery(document).ready(function($) {

		/* For Upload */
		var drop = $( "#cc-icons-files" );
		drop.on( 'dragenter', function( e ) {
			$( ".cc-icons-drop" ).css( {
				"border": "4px dashed #09f",
				"background": "rgba(0, 153, 255, .05)"
			} );
			$( ".cc-icons-cont" ).css( {
				"color": "#09f"
			} );
		} ).on( 'dragleave dragend mouseout drop', function( e ) {
			$( ".cc-icons-drop" ).css( {
				"border": "3px dashed #DADFE3",
				"background": "transparent"
			} );
			$( ".cc-icons-cont" ).css( {
				"color": "#8E99A5"
			} );
		} );

		var timeout;

		function progress_bar( max, time ) {
			$( '.cc-icons-progress .bar' ).stop().animate({"width": max + '%'}, time );
		}

		function reloadCss() {
			$( '#cci-icon-fonts-css' ).each( function() {
				this.href = this.href.replace( /\?.*|$/, '?ver=' + Math.random( 0, 1000 ) );
			} );
		}

		function ajaxSend( request, callback, context ) {

			$.ajax( {
				url: CC_ICONS.ajaxurl,
				data: request,
				cache: false,
				contentType: false,
				dataType: 'json',
				context: context,
				processData: false,
				type: 'POST',
				success: function( response, textStatus ) {
					callback( response, this );
				}

			} );
		}

		/* New Font */
		$( '#cc-icons-files' ).on( 'change', function( evt ) {

			var reader = new FileReader(),
				files = evt.target.files,
				files_one = files[ 0 ];

			progress_bar( 100, 200 );

			// Closure to capture the file information.
			reader.onload = (function( theFile ) {
				return function( e ) {

					var file_name = escape( theFile.name );

					$( '.cc-icons-tit,.cc-icons-desc,.cc-icons-browse' ).hide();
					$( '.cc-icons-progress' ).addClass( 'show' );

					var request = new FormData();

					request.append( "file_name", file_name );
					request.append( "source_file", theFile, file_name );
					request.append( "action", "cc_icons_save_font" );
					request.append( "_wpnonce", $( '.cc-icons-drop' ).find( '#_wpnonce' ).val() );

					ajaxSend( request, function( response ) {

						if ( response.status_save === 'updated' ) {

							var $el = $( '.cc-icons-clone' ).clone();
							$el.find( '.cci-extension-title' ).text( response.name );
							$el.find( '.cci-extension-info-details .value' ).text( response.count_icons );
							$el.find( '.cs-fa' ).addClass( response.first_icon );
							$el.find( '.iconlist' ).html( response.iconlist );

							$( '.wrapper-list-fonts' )
								.removeClass( 'hidden' )
								.find( '.cci-extensions' ).append( $el[ 0 ] );

							$el.show().find( '.cci-delete-font' ).attr( 'data-font', JSON.stringify( response.data ) );

							reloadCss();
						}
						else if ( response.status_save === 'exist' ) {
							alert( CC_ICONS.exist );
						} 
						else if ( response.status_save === 'failedopen' ) {
							alert( CC_ICONS.failedopen );
						} 
						else if ( response.status_save === 'failedextract' ) {
							alert( CC_ICONS.failedextract );
						} 
						else if ( response.status_save === 'emptyfile' ) {
							alert( CC_ICONS.emptyfile );
						}
						else if ( response.status_save === 'updatefailed' ) {
							alert( CC_ICONS.updatefailed );
						}

						progress_bar( 0, 5 );
						$( '.cc-icons-progress' ).removeClass( 'show' );
						$( '.cc-icons-tit,.cc-icons-desc,.cc-icons-browse' ).show();

					} );

				}
			})( files_one );

			reader.readAsDataURL( files_one );
		} );

		/* Delete Font */
		$( document).on( 'click', '.cci-delete-font', function( e ) {
			e.preventDefault();

			var conf = confirm( CC_ICONS.delete );
			
			if(conf == true){

			var request = new FormData(),
				$this = $( this ),
				data = $this.data( 'font' );

			request.append( "file_name", data.name );
			request.append( "action", "cc_icons_delete_font" );
			request.append( "_wpnonce", $( '.cc-icons-drop' ).find( '#_wpnonce' ).val() );

			ajaxSend( request, function( response, context ) {

				if ( response.status_save === 'remove' ) {

					var $item = $( context ).closest( '.cci-extension' );
					$item.css( {
						'transition': 'all 0.5s',
						'transform': 'scale(0)'
					} );

					setTimeout( function() {

						$item.remove( 0 );
						if ( !$( '.cci-extension:visible' ).length ) {
							$( '.wrapper-list-fonts' ).addClass( 'hidden' );
						}

						reloadCss();

					}, 1500 );

				}

			}, this );

			}

		} );

		/* Regen */
		$( '.cc-icons-regen' ).on( 'click', function( e ) {

			e.preventDefault();

			var request = new FormData();
			request.append( "action", "cc_icons_regenerate" );

			ajaxSend( request, function(response) {

				if ( response.status_regen === 'regen' ) {
					alert(CC_ICONS.regen);
				} else {
					alert("Unknown error...");
				}

			} );

		} );

		/* FUTURE: status change*/
		$( '.wrapper-list-fonts' ).on( 'click', '.cci-extension-status-icon', function( e ) {

			e.preventDefault();

			var request = new FormData(),
				$this = $( this ),
				data = $this.closest( '.font-item' ).find( '.cci-delete-font' ).data( 'font' );

			request.append( "file_name", data.name );
			request.append( "action", "cc_icons_change_status" );
			request.append( "_wpnonce", $( '.cc-icons-drop' ).find( '#_wpnonce' ).val() );

			if ( $this.hasClass( 'enabled' ) ) {
				request.append( "sub_action", "enabled" );
			} else {
				request.append( "sub_action", "disabled" );
			}

			ajaxSend( request, function() {

			} );

		} );

		$('.cci-main').on('click', '.cci-extension-info-details', function(){
			$(this).next( $('.iconlist') ).slideToggle('slow');
		});
	}
);