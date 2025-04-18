<?php
/**
 * @version		$Header$
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
require_once( '../kernel/includes/setup_inc.php' );
include_once( NEWSLETTERS_PKG_CLASS_PATH.'BitNewsletterMailer.php' );

$gBitSystem->verifyPackage( 'newsletters' );

if( !$gBitUser->isRegistered() && !$gBitUser->hasPermission( 'p_newsletters_subscribe' ) && empty( $_REQUEST['c'] ) ) {
	$gBitSystem->fatalError( tra("You must be logged in to subscribe to newsletters"));
}

require_once( NEWSLETTERS_PKG_INCLUDE_PATH.'lookup_newsletter_inc.php' );

$feedback = array();

/* List newsletters */
$listHash = array();
$newsletters = $gContent->getList( $listHash );
$gBitSmarty->assignByRef( 'subs', BitNewsletter::getUserSubscriptions( $gBitUser->getField( 'user_id' ), $gBitUser->getField( 'email' ) ) );
$gBitSmarty->assignByRef('newsletters', $newsletters );

$foo = parse_url($_SERVER["REQUEST_URI"]);
$gBitSmarty->assign('url_subscribe', httpPrefix(). $foo["path"]);

$subinfo = array();
$unsubs = array();

// We have a url_code from a clicked link in an email
if( isset( $_REQUEST['c'] ) && strlen( $_REQUEST['c'] ) == 32 && ($subInfo = BitNewsletterMailer::lookupSubscription( array( 'url_code' => $_REQUEST['c'] ) )) ) {
} elseif( $gBitUser->isRegistered() ) {
	if( !$subInfo = BitNewsletterMailer::lookupSubscription( array( 'user_id' => $gBitUser->mUserId ) ) ) {
		$subInfo = $gBitUser->mInfo;
	}
}
if( !empty( $subInfo['user_id'] ) && BitBase::verifyId( $subInfo['user_id'] ) ) {
	$lookup['user_id'] = $subInfo['user_id'];
} else {
	$lookup['email'] = $subInfo['email'];
}

$unsubs = BitNewsletterMailer::getUnsubscriptions( $lookup );

// Update subscriptions
if( isset( $_REQUEST["update"] ) ) {
	$subHash['response_content_id'] = $_REQUEST['response_content_id'];
	$subHash['sub_lookup'] = !empty( $subInfo['user_id'] ) ? array( 'user_id' => $subInfo['user_id'] ) : array( 'email' => $subInfo['email'] );

	if( !empty( $_REQUEST['unsubscribe_all'] ) ) {
		$subHash['unsubscribe_all'] = 'y';
		$subHash['unsub_content'] = array_keys( $newsletters );
	} else {
		$subHash['unsubscribe_all'] = NULL;
		foreach( array_keys( $newsletters ) as $nlContentId ) {
			if( empty( $_REQUEST['nl_content_id'] ) || !in_array( $nlContentId, $_REQUEST['nl_content_id'] ) ) {
				$subHash['unsub_content'][] = $nlContentId;
			}
		}
	}

	if( BitNewsletterMailer::storeSubscriptions( $subHash ) ) {
		$feedback['success'] = tra( "Your subscriptions were updated." );
	} else {
		$feedback['error'] = tra( "Subscriptions were not updated." );
	}
	$unsubs = BitNewsletterMailer::getUnsubscriptions( $lookup );
}

if( isset( $_REQUEST["subscribe"] ) && !empty( $_REQUEST["email"] ) ) {
	$gBitSystem->verifyPermission( 'p_newsletters_subscribe' );
	$feedback['success'] = tra( "Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed." );

	if( !$gBitUser->hasPermission( 'p_subscribe_email' ) ) {
		$_REQUEST["email"] = $gBitUser->mInfo['email'];
	}

	// Now subscribe the email address to the newsletter
	$gContent->subscribe( $_REQUEST["email"], TRUE, TRUE );
}

foreach( $unsubs as $sub ) {
	if( !empty( $sub['unsubscribe_all'] ) ) {
		$subInfo['unsubscribe_all'] = TRUE;
		break;
	}
}

$gBitSmarty->assign( 'subInfo', $subInfo );
$gBitSmarty->assign( 'unsubs', $unsubs );
$mid = 'bitpackage:newsletters/user_subscriptions.tpl';
$title = "Newsletter Subscriptions";

$gBitSmarty->assign( 'feedback', $feedback );

// Display the template
$gBitSystem->display( $mid, $title , array( 'display_mode' => 'display' ));

?>
