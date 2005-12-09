<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.3 2005/12/09 15:55:45 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
if( $gBitSystem->isPackageActive( 'webmail' ) ) {
	include_once( WEBMAIL_PKG_PATH.'htmlMimeMail.php' );
}
$gBitSystem->verifyPackage( 'newsletters' );

$gBitSmarty->assign('confirm', 'n');
if( isset( $_REQUEST["confirm_subscription"] ) ) {
	if( $conf = $nllib->confirm_subscription( $_REQUEST["confirm_subscription"] ) ) {
		$gBitSmarty->assign( 'confirm', 'y' );
		$gBitSmarty->assign( 'nl_info', $conf );
	}
}

if( isset( $_REQUEST["unsubscribe"] ) ) {
	if( $conf = $nllib->unsubscribe( $_REQUEST["unsubscribe"] ) ) {
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
	$nllib->newsletter_subscribe( $_REQUEST["nl_id"], $_REQUEST["email"] );
}

if( isset( $_REQUEST["info"] ) ) {
	$nl_info = $nllib->get_newsletter($_REQUEST["nl_id"]);

	$gBitSmarty->assign( 'nl_info', $nl_info );
	$gBitSmarty->assign( 'subscribe', 'y' );
}
/* List newsletters */
$listHash = array();
$channels = $nllib->getList( $listHash );

for ($i = 0; $i < count($channels["data"]); $i++) {
/*
	if ($userlib->object_has_one_permission($channels["data"][$i]["nl_id"], 'newsletters')) {
		$channels["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $channels["data"][$i]["nl_id"], 'newsletter', 'tiki_p_subscribe_newsletters')) {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'n';
		}

		if ($tiki_p_admin == 'y'
			|| $userlib->object_has_permission($user, $channels["data"][$i]["nl_id"], 'newsletter', 'tiki_p_admin_newsletters')) {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
*/
}

$cant_pages = ceil( $channels["cant"] / $listHash['max_records'] );
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign( 'actual_page', 1 + ( $listHash['offset'] / $listHash['max_records'] ) );

if( $channels["cant"] > ( $listHash['offset'] + $listHash['max_records'] ) ) {
	$gBitSmarty->assign( 'next_offset', $offset + $listHash['max_records'] );
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if( $listHash['offset'] > 0) {
	$gBitSmarty->assign('prev_offset', $listHash['offset'] - $listHash['max_records']);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('channels', $channels["data"]);
$gBitSmarty->assign( 'feedback', $feedback);

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/newsletters.tpl');

?>
