<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.2 2005/12/09 20:24:55 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( UTIL_PKG_PATH.'htmlMimeMail.php' );

$gBitSystem->verifyPackage( 'newsletters' );


require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$listHash = array();
$newsletters = $gContent->mNewsletter->getList( $listHash );
$gBitSmarty->assign( 'newsletters', $newsletters );

if (isset($_REQUEST["remove"] ) && $gContent->isValid() ) {
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = TRUE;
		$formHash['edition_id'] = $gContent->mEditionId;
		$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the newsletter edition '.$gContent->getTitle().'?' ) );
	} else {
		if( $gContent->expunge() ) {
			header( "Location: ".NEWSLETTERS_PKG_URL.'admin/' );
			die;
		}
	}
}

if (isset($_REQUEST["template_id"]) && $_REQUEST["template_id"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["template_id"]);

	$_REQUEST["data"] = $template_data["content"];
	$_REQUEST["preview"] = 1;
}

$gBitSmarty->assign('preview', 'n');

if (isset($_REQUEST["preview"])) {
	$gBitSmarty->assign('preview', 'y');

	//$parsed = $tikilib->parse_data($_REQUEST["content"]);
	$parsed = $_REQUEST["data"];
	$gBitSmarty->assign('parsed', $parsed);
	$info["data"] = $_REQUEST["data"];
	$info["subject"] = $_REQUEST["subject"];
	$gBitSmarty->assign('info', $info);
}

$gBitSmarty->assign('presend', 'n');

if (isset($_REQUEST["save"])) {
	// Now send the newsletter to all the email addresses and save it in sent_newsletters
	$gBitSmarty->assign('presend', 'y');

	$subscribers = $nllib->get_subscribers($_REQUEST["nl_id"]);
	$gBitSmarty->assign('nl_id', $_REQUEST["nl_id"]);
	$gBitSmarty->assign('data', $_REQUEST["data"]);
	$gBitSmarty->assign('subject', $_REQUEST["subject"]);
	$cant = count($subscribers);
	$gBitSmarty->assign('subscribers', $cant);
}

$gBitSmarty->assign('emited', 'n');

if (isset($_REQUEST["send"])) {
	$subscribers = $nllib->get_subscribers($_REQUEST["nl_id"]);

	$mail = new htmlMimeMail();
	$mail->setFrom('noreply@noreply.com');
	$mail->setSubject($_REQUEST["subject"]);
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
		$mail->setHTML($_REQUEST["data"] . $unsubmsg, strip_tags($_REQUEST["data"]));

		if ($mail->send($to_array, 'mail'))
			$sent++;
	}

	$gBitSmarty->assign('sent', $sent);
	$gBitSmarty->assign('emited', 'y');
	$nllib->replace_edition($_REQUEST["nl_id"], $_REQUEST["subject"], $_REQUEST["data"], $sent);
}

$gEdition = new BitNewsletterEdition();
$listHash = array();
$editions = $gEdition->getList( $listHash );
$gBitSmarty->assign_by_ref( 'editions', $editions );
$gBitSmarty->assign( 'listInfo', $listHash );

if( $gBitSystem->isFeatureActive( 'tiki_p_use_content_templates' ) ) {
	$templates = $tikilib->list_templates('newsletters', 0, -1, 'name_asc', '');
	$gBitSmarty->assign_by_ref('templates', $templates["data"]);
}

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl');

?>

