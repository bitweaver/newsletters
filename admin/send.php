<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.12 2006/03/23 16:40:40 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );

$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'tiki_p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$feedback = array();

if( @BitBase::verifyId( $_REQUEST["template_id"] ) ) {
	$template_data = $tikilib->get_template($_REQUEST["template_id"]);

	$_REQUEST['edit'] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

$gBitSmarty->assign('preview', 'n');
$gBitSmarty->assign('presend', 'n');
$gBitSmarty->assign('emited', 'n');

if( $gContent->isValid() && isset( $_REQUEST['preview'] ) ) {
	$recipients = $gContent->getRecipients( $_REQUEST['send_group'] );
	$gBitSmarty->assign_by_ref( 'recipientList', $recipients );
	$gBitSmarty->assign( 'sending', TRUE );
} elseif( $gContent->isValid() && isset( $_REQUEST["send"] ) ) {
	if( $emails = $gContent->getRecipients( $_REQUEST['send_group'] ) ) {
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
	$gBitSmarty->assign_by_ref( 'groupList', $groups['data'] );
} else {
	$listHash = array();
	$editions = $gContent->getList( $listHash );
	$gBitSmarty->assign_by_ref( 'editionList', $editions );
	$gBitSmarty->assign( 'listInfo', $listHash );
}

$gBitSmarty->assign_by_ref( 'feedback', $feedback );
// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl');

?>

