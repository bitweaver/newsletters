<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.23 2007/01/06 06:22:12 spiderr Exp $

// Copyright (c) 2006 - bitweaver.org - Christian Fowler, Max Kremmel, et. al
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );

$gBitSystem->verifyPackage( 'newsletters' );

if( !$gBitUser->isRegistered() && !$gBitUser->hasPermission( 'p_newsletters_subscribe' ) && empty( $_REQUEST["sub"] ) ) {
	$gBitSystem->fatalError( tra("You must be logged in to subscribe to newsletters"));
}

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

$feedback = array();

/* List newsletters */
$listHash = array();

$newsletters = $gContent->getList( $listHash );
foreach( array_keys( $newsletters ) as $contentId ) {
	$listHash = array( 'nl_id' => $newsletters[$contentId]['nl_id']  );
	$newsletters[$contentId]['editions'] = BitNewsletterEdition::getList( $listHash );
}

$gBitSmarty->assign_by_ref( 'subs', BitNewsletter::getUserSubscriptions( $gBitUser->getField( 'user_id' ), $gBitUser->getField( 'email' ) ) );
$gBitSmarty->assign_by_ref('newsletters', $newsletters );

$mid = 'bitpackage:newsletters/newsletters.tpl';
$title = "List Newsletters";

$gBitSmarty->assign( 'feedback', $feedback );

// Display the template
$gBitSystem->display( $mid, $title );

?>
