<?php

require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterMailer.php' );
global $gBitSystem, $gBitUser;

$gBitSystem->verifyPackage( 'newsletters' );

if( !$gBitUser->isRegistered() && !$gBitUser->hasPermission( 'p_newsletters_subscribe' ) && empty( $_REQUEST["sub"] ) ) {
	$gBitSystem->fatalError( tra("You must be logged in to subscribe to newsletters"));
}

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

/* List newsletters */
$listHash = array();

$newsletters = BitNewsletter::getList( $listHash );
foreach( array_keys( $newsletters ) as $contentId ) {
	$listHash = array( 'nl_id' => $newsletters[$contentId]['nl_id']  );
	$newsletters[$contentId]['editions'] = BitNewsletterEdition::getList( $listHash );
}

$gBitSmarty->assign_by_ref( 'subs', BitNewsletter::getUserSubscriptions( $gBitUser->getField( 'user_id' ), $gBitUser->getField( 'email' ) ) );
$gBitSmarty->assign_by_ref('newsletters', $newsletters );



?>
