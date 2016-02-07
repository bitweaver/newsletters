<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../../kernel/setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitNewsletterMailer.php' );
include_once( NEWSLETTERS_PKG_PATH.'BitNewsletterEdition.php' );

$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'p_send_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$feedback = array();

if( @BitBase::verifyId( $_REQUEST["edition_id"] ) ) {
	$gContent->mEditionId = $_REQUEST["edition_id"];
	$gContent->load();

	$_REQUEST['edit'] = $gContent->mInfo['data'];
//	$_REQUEST["preview"] = 1;
}

$gBitSmarty->assign('preview', 'n');
$gBitSmarty->assign('presend', 'n');
$gBitSmarty->assign('emited', 'n');
$validated = !empty( $_REQUEST["validated"] ) ? TRUE : FALSE;

if( $gContent->isValid() && isset( $_REQUEST['preview'] ) && isset( $_REQUEST['send_group'] ) ) {
	$recipients = $gContent->getRecipients( $_REQUEST['send_group'], $validated, !empty( $_REQUEST['test_mode'] ) );
	$gBitSmarty->assignByRef( 'recipientList', $recipients );
	$gBitSmarty->assign( 'validated', $validated );
	$gBitSmarty->assign( 'sending', TRUE );
} elseif( $gContent->isValid() && isset( $_REQUEST["send"] ) ) {
	if( $emails = $gContent->getRecipients( $_REQUEST['send_group'], $validated, !empty( $_REQUEST['test_mode'] ) ) ) {
		global $gBitNewsletterMailer;
		$gBitNewsletterMailer = new BitNewsletterMailer();
		$queueCount = $gBitNewsletterMailer->queueRecipients( $gContent->mContentId, $gContent->mNewsletter->mContentId, $emails, !empty( $_REQUEST['test_mode'] ) );
		$feedback['success'] = $queueCount.' '.tra( 'emails were queued to be sent:' ).' '.$gContent->getTitle();
		$gContent->mEditionId = NULL;
	} else {
		$feedback['error'] = tra( 'No emails were queued.' );
	}
}

if( $gContent->isValid() ) {
	$groupListHash = array();
	$groups = $gBitUser->getAllGroups( $groupListHash );
	$groups['send_subs']['group_name'] = 'Send to subscribers';
	$gBitSmarty->assignByRef( 'groupList', $groups );
} else {
	$listHash = array();
	$editions = $gContent->getList( $listHash );
	$gBitSmarty->assignByRef( 'editionList', $editions );

/*	if( $gBitSystem->isFeatureActive( 'bit_p_use_content_templates' ) ) {
		$templates = $bitlib->list_templates('newsletters', 0, -1, 'name_asc', '');
		$gBitSmarty->assignByRef('templates', $templates["data"]);
	}*/
}

$gBitSmarty->assignByRef( 'feedback', $feedback );
// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl' , tra( "Send Newsletter" ).': '.$gContent->getTitle() , array( 'display_mode' => 'admin' ));

?>

