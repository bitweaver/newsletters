<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.6 2005/12/11 08:22:51 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( UTIL_PKG_PATH.'htmlMimeMail.php' );

$gBitSystem->verifyPackage( 'newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

if (isset($_REQUEST["template_id"]) && $_REQUEST["template_id"] > 0) {
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
	$emails = $gContent->getRecipients( $_REQUEST['send_group'] );
vd( $emails );
vd( $_REQUEST );
die;

	$mail = new htmlMimeMail();
	$mail->setFrom('noreply@noreply.com');
	$mail->setSubject($_REQUEST['title']);
	$sent = 0;

	foreach ($subscribers as $email) {
		$to_array = array();

		$to_array[] = $email;
		if ($nl_info["unsub_msg"] = 'y') {
			$unsubmsg = $nllib->get_unsub_msg($_REQUEST["nl_id"], $email);
		} else {
			$unsubmsg = ' ';
		}
		$mail->setHeadCharset("utf-8");
		$mail->setTextCharset("utf-8");
		$mail->setHtmlCharset("utf-8");
		$mail->setFrom($sender_email);
		$mail->setHTML($_REQUEST['edit'] . $unsubmsg, strip_tags($_REQUEST['edit']));

		if ($mail->send($to_array, 'mail'))
			$sent++;
	}

	$gBitSmarty->assign('sent', $sent);
	$gBitSmarty->assign('emited', 'y');
	$nllib->replace_edition($_REQUEST["nl_id"], $_REQUEST['title'], $_REQUEST['edit'], $sent);
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

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl');

?>

