<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.16.2.2 2006/02/11 14:17:58 wolff_borg Exp $

// Copyright (c) 2006 - bitweaver.org - Christian Fowler, Max Kremmel, et. al
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );

$gBitSystem->verifyPackage( 'newsletters' );

if( !$gBitUser->isRegistered() && !$gBitUser->hasPermission( 'bit_p_subscribe_newsletters' ) && empty( $_REQUEST["sub"] ) ) {
	$gBitSystem->fatalError( tra("You must be logged in to subscribe to newsletters"));
}

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

$feedback = array();

/* List newsletters */
$listHash = array();
if( !empty( $_REQUEST['nl_id'] ) ) {
	$listHash['nl_id'] = $_REQUEST['nl_id'];
}
$newsletters = $gContent->getList( $listHash );
$gBitSmarty->assign_by_ref('newsletters', $newsletters );

$foo = parse_url($_SERVER["REQUEST_URI"]);
$gBitSmarty->assign('url_subscribe', httpPrefix(). $foo["path"]);

if (isset($_REQUEST["sub"])) {
	$gContent->confirmSubscription($_REQUEST["sub"]);
	$gBitSmarty->assign('confirm', 'y');
}

if( isset( $_REQUEST["sub"] ) || $gBitUser->isRegistered() ) {
	if( isset( $_REQUEST["sub"] ) && strlen( $_REQUEST["sub"] ) == 32 && ($subInfo = BitMailer::lookupSubscription( array( 'url_code' => $_REQUEST["sub"] ) )) ) {
		$lookup['email'] = $subInfo['email'];
		$unsubs = BitMailer::getUnsubscriptions( $lookup );
	} else {
		if( !$subInfo = BitMailer::lookupSubscription( array( 'user_id' => $gBitUser->mUserId ) ) ) {
			$subInfo = $gBitUser->mInfo;
		}
		$lookup['user_id'] = $gBitUser->mUserId;
		$unsubs = BitMailer::getUnsubscriptions( $lookup );
	}
	if( isset( $_REQUEST["update"] ) ) {
		$subHash['response_content_id'] = $_REQUEST['response_content_id'];
		$subHash['sub_lookup'] = !empty( $subInfo['user_id'] ) ? array( 'user_id' => $subInfo['user_id'] ) : array( 'email' => $subInfo['email'] );

		if( !empty( $_REQUEST['unsubscribe_all'] ) ) {
			$subHash['unsubscribe_all'] = 'y';
			$subHash['unsub_content'] = array_keys( $newsletters );
		} else {
			$subHash['unsubscribe_all'] = NULL;
		}

		foreach( array_keys( $newsletters ) as $nlContentId ) {
			if( empty( $_REQUEST['nl_content_id'] ) || !in_array( $nlContentId, $_REQUEST['nl_content_id'] ) ) {
				$subHash['unsub_content'][] = $nlContentId;
			}
		}

		if( BitMailer::storeSubscriptions( $subHash ) ) {
			$feedback['success'] = tra( "Your subscriptions were updated." );
		} else {
			$feedback['error'] = tra( "Subscriptions were not updated." );
		}
		$unsubs = BitMailer::getUnsubscriptions( $lookup );
		foreach( $unsubs as $sub ) {
			if( !empty( $sub['unsubscribe_all'] ) ) {
				$subInfo['unsubscribe_all'] = TRUE;
				break;
			}
		}
	}
	$gBitSmarty->assign( 'subInfo', $subInfo );
	$gBitSmarty->assign( 'unsubs', $unsubs );
	$mid = 'bitpackage:newsletters/user_subscriptions.tpl';
}

$user_email = $gBitUser->isRegistered() ? $gBitUser->mInfo['email'] : '';

$gBitSmarty->assign('email', $user_email);

if( isset( $_REQUEST["subscribe"] ) && !empty( $_REQUEST["email"] ) ) {
	$gBitSystem->verifyPermission( 'bit_p_subscribe_newsletters' );
	$feedback['success'] = tra( "Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed." );

	if( !$gBitUser->hasPermission( 'bit_p_subscribe_email' ) ) {
		$_REQUEST["email"] = $gBitUser->mInfo['email'];
	}

	// Now subscribe the email address to the newsletter
	$gContent->subscribe( $_REQUEST["email"] );
}

$subscribe = false;
if (isset($_REQUEST["info"])) {
	$subscribe = true;
	$gBitSmarty->assign('subscribe', 'y');
}

/*if( !$subscribe && $gContent->isValid() ) {
	$mid = 'bitpackage:newsletters/view_newsletter.tpl';
	$title = "View Newsletter";
} else*/ {
	/* List newsletters */
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
	$mid = 'bitpackage:newsletters/newsletters.tpl';
	$title = "List Newsletters";
}

$gBitSmarty->assign( 'feedback', $feedback );

// Display the template
$gBitSystem->display( $mid, $title );

?>
