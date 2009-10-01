<?php
/**
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * created 2005/12/10
 *
 * @package newsletters
 * @author spider <spider@steelsun.com>
 */

/** 
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitNewsletterMailer.php' );

if( isset( $_REQUEST["sub"] ) || $gBitUser->isRegistered() ) {
	if( isset( $_REQUEST["sub"] ) && strlen( $_REQUEST["sub"] ) == 32 && ($subInfo = BitNewsletterMailer::lookupSubscription( array( 'url_code' => $_REQUEST["sub"] ) )) ) {
		BitNewsletterMailer::trackMail( $subInfo['url_code'] );
	}
}

// open the file in a binary mode
$trackImage = $gBitSystem->getConfig( 'newsletter_tracking_image', NEWSLETTERS_PKG_PATH.'images/track.gif' );

if( $fp = fopen( $trackImage, 'rb') ) {
	// send the right headers
	header( "Content-Type: image/png" );
	header( "Content-Length: " . filesize( $trackImage ) );

	// dump the picture and stop the script
	fpassthru( $fp );
}

exit;

?>