<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.8 2005/12/20 22:05:07 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );

$gBitSystem->verifyPackage( 'newsletters' );

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
} elseif( $gContent->isValid() && isset( $_REQUEST["send"] ) ) {
	if( $emails = $gContent->getRecipients( $_REQUEST['send_group'] ) ) {
		global $gBitMailer;
		$gBitMailer = new BitMailer();
		$gBitMailer->queueRecipients( $gContent->mContentId, $emails );
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

	if( $gBitSystem->isFeatureActive( 'tiki_p_use_content_templates' ) ) {
		$templates = $tikilib->list_templates('newsletters', 0, -1, 'name_asc', '');
		$gBitSmarty->assign_by_ref('templates', $templates["data"]);
	}
}

$gBitSmarty->assign_by_ref( 'feedback', $feedback );
// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl');

?>

