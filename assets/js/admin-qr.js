/* WP QR Menu — generate table QR codes client-side, download as PNG. */
( function () {
	"use strict";

	function labelFor( card ) {
		var strong = card ? card.querySelector( ".qrm-card-meta strong" ) : null;
		var text = strong ? strong.textContent.trim() : "table";
		return text.toLowerCase().replace( /\s+/g, "-" );
	}

	function downloadPng( url, name ) {
		var qr = qrcode( 0, "M" );
		qr.addData( url );
		qr.make();

		var image = new Image();
		image.onload = function () {
			var canvas = document.createElement( "canvas" );
			canvas.width = image.width;
			canvas.height = image.height;
			var ctx = canvas.getContext( "2d" );
			ctx.fillStyle = "#ffffff";
			ctx.fillRect( 0, 0, canvas.width, canvas.height );
			ctx.drawImage( image, 0, 0 );

			var link = document.createElement( "a" );
			link.href = canvas.toDataURL( "image/png" );
			link.download = "qrmenu-" + name + ".png";
			document.body.appendChild( link );
			link.click();
			document.body.removeChild( link );
		};
		image.src = qr.createDataURL( 16, 4 );
	}

	document.querySelectorAll( ".qrm-card-code" ).forEach( function ( holder ) {
		var url = holder.getAttribute( "data-qrm-url" );
		if ( ! url || typeof qrcode === "undefined" ) {
			return;
		}

		var qr = qrcode( 0, "M" );
		qr.addData( url );
		qr.make();
		holder.innerHTML = qr.createImgTag( 6, 8 );

		var card = holder.closest( ".qrm-card" );
		var button = card ? card.querySelector( ".qrm-download" ) : null;
		if ( button ) {
			button.addEventListener( "click", function () {
				downloadPng( url, labelFor( card ) );
			} );
		}
	} );

	var printButton = document.getElementById( "qrm-print" );
	if ( printButton ) {
		printButton.addEventListener( "click", function () {
			window.print();
		} );
	}
} )();
