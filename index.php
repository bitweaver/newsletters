<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.7 2005/12/11 06:34:19 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

if( $gBitSystem->isPackageActive( 'webmail' ) ) {
	include_once( WEBMAIL_PKG_PATH.'htmlMimeMail.php' );
}
$gBitSystem->verifyPackage( 'newsletters' );

$gBitSmarty->assign('confirm', 'n');
if( isset( $_REQUEST["confirm_subscription"] ) ) {
	if( $conf = $gContent->confirmSubscription( $_REQUEST["confirm_subscription"] ) ) {
		$gBitSmarty->assign( 'confirm', 'y' );
		$gBitSmarty->assign( 'nl_info', $conf );
	}
}

if( isset( $_REQUEST["unsubscribe"] ) ) {
	if( $conf = $gContent->unsubscribe( $_REQUEST["unsubscribe"] ) ) {
		$feedback['success'] = tra( "Your email address was removed from the list of subscriptors." );
		$gBitSmarty->assign('nl_info', $conf);
	}
}

if( !$gBitUser->isRegistered() && !$gBitUser->hasPermission( 'bit_p_subscribe_newsletters' ) && empty( $_REQUEST["confirm_subscription"] ) ) {
	$gBitSystem->fatalError( tra("You must be logged in to subscribe to newsletters"));
}

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );
$feedback = array();

$foo = parse_url($_SERVER["REQUEST_URI"]);
$gBitSmarty->assign('url_subscribe', httpPrefix(). $foo["path"]);

$user_email = $gBitUser->isRegistered() ? $gBitUser->mInfo['email'] : '';

$gBitSmarty->assign('email', $user_email);

if( isset( $_REQUEST["subscribe"] ) ) {
	$gBitSystem->verifyPermission( 'bit_p_subscribe_newsletters' );
	$feedback['success'] = tra( "Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed." );

	if( !$gBitUser->hasPermission( 'tiki_p_subscribe_email' ) ) {
		$_REQUEST["email"] = $gBitUser->mInfo['email'];
	}

	// Now subscribe the email address to the newsletter
	$gContent->subscribe( $_REQUEST["email"] );
}

if( $gContent->isValid() ) {
	$mid = 'bitpackage:newsletters/view_newsletter.tpl';
} else {
	/* List newsletters */
	$listHash = array();
	$newsletters = $gContent->getList( $listHash );
	/*
	for( $i = 0; $i < count( $newsletters ); $i++ ) {
		if ($userlib->object_has_one_permission($newsletters["data"][$i]["nl_id"], 'newsletters')) {
			$newsletters["data"][$i]["individual"] = 'y';

			if ($userlib->object_has_permission($user, $newsletters["data"][$i]["nl_id"], 'newsletter', 'tiki_p_subscribe_newsletters')) {
				$newsletters["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
			} else {
				$newsletters["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'n';
			}

			if ($tiki_p_admin == 'y'
				|| $userlib->object_has_permission($user, $newsletters["data"][$i]["nl_id"], 'newsletter', 'tiki_p_admin_newsletters')) {
				$newsletters["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
			}
		} else {
			$newsletters["data"][$i]["individual"] = 'n';
		}
	}
	*/
	$gBitSmarty->assign_by_ref('newsletters', $newsletters );
	$gBitSmarty->assign( 'feedback', $feedback );
	$mid = 'bitpackage:newsletters/newsletters.tpl';
}

// Display the template
$gBitSystem->display( $mid );

?>
