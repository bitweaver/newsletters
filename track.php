<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/track.php,v 1.1 2005/12/29 17:22:47 spiderr Exp $

// Copyright (c) 2006 - bitweaver.org - Christian Fowler, Max Kremmel, et. al
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );

if( isset( $_REQUEST["sub"] ) || $gBitUser->isRegistered() ) {
	if( isset( $_REQUEST["sub"] ) && strlen( $_REQUEST["sub"] ) == 32 && ($subInfo = BitMailer::lookupSubscription( array( 'url_code' => $_REQUEST["sub"] ) )) ) {
		BitMailer::trackMail( $subInfo['url_code'] );
	}
}

// open the file in a binary mode
$trackImage = $gBitSystem->getPreference( 'newsletter_tracking_image', NEWSLETTERS_PKG_PATH.'images/track.gif' );

if( $fp = fopen( $trackImage, 'rb') ) {
	// send the right headers
	header( "Content-Type: image/png" );
	header( "Content-Length: " . filesize( $trackImage ) );

	// dump the picture and stop the script
	fpassthru( $fp );
}

exit;

?>