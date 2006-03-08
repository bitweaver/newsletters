<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.11.2.4 2006/03/08 11:47:32 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitNewsletterEdition.php' );

$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'bit_p_send_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$feedback = array();

if( isset( $_REQUEST["edition_id"] ) ) {
	$gContent->mEditionId = $_REQUEST["edition_id"];
	$gContent->load();

	$_REQUEST['edit'] = $gContent->mInfo['data'];
//	$_REQUEST["preview"] = 1;
}

$gBitSmarty->assign('preview', 'n');
$gBitSmarty->assign('presend', 'n');
$gBitSmarty->assign('emited', 'n');
$validated = (isset($_REQUEST["validated"]) && !empty($_REQUEST["validated"])) ? TRUE : FALSE;

if( $gContent->isValid() && isset( $_REQUEST['preview'] ) && isset( $_REQUEST['send_group'] ) ) {
	$recipients = $gContent->getRecipients( $_REQUEST['send_group'], $validated );
	$gBitSmarty->assign_by_ref( 'recipientList', $recipients );
	$gBitSmarty->assign( 'validated', $validated );
	$gBitSmarty->assign( 'sending', TRUE );
} elseif( $gContent->isValid() && isset( $_REQUEST["send"] ) ) {
	if( $emails = $gContent->getRecipients( $_REQUEST['send_group'], $validated ) ) {
		global $gBitMailer;
		$gBitMailer = new BitMailer();
		$gBitMailer->queueRecipients( $gContent->mContentId, $gContent->mNewsletter->mContentId, $emails );
		$feedback['success'] = count( $emails ).' '.tra( 'emails were queued to be sent:' ).' '.$gContent->getTitle();
		$gContent->mEditionId = NULL;
	} else {
		$feedback['error'] = tra( 'No emails were queued.' );
	}
}

if( $gContent->isValid() ) {
	$groupListHash = array();
	$groups = $gBitUser->getAllGroups( $groupListHash );
	$groups['data']['send_subs']['group_name'] = 'Send to subscribers';
	$gBitSmarty->assign_by_ref( 'groupList', $groups['data'] );
} else {
	$listHash = array();
	$editions = $gContent->getList( $listHash );
	$gBitSmarty->assign_by_ref( 'editionList', $editions );

/*	if( $gBitSystem->isFeatureActive( 'bit_p_use_content_templates' ) ) {
		$templates = $bitlib->list_templates('newsletters', 0, -1, 'name_asc', '');
		$gBitSmarty->assign_by_ref('templates', $templates["data"]);
	}*/
}

$gBitSmarty->assign_by_ref( 'feedback', $feedback );
// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl' , "Send Newsletters");

?>

