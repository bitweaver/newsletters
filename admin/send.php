<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/send.php,v 1.1 2005/12/09 13:30:48 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( UTIL_PKG_PATH.'htmlMimeMail.php' );

$gBitSystem->verifyPackage( 'newsletters' );


require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

$listHash = array();
$newsletters = $nllib->getList( $listHash );
$gBitSmarty->assign('newsletters', $newsletters["data"]);

$nl_info = $nllib->get_newsletter($_REQUEST["nl_id"]);
// $nl_info["name"] = '';
// $nl_info["description"] = '';
// $nl_info["allow_user_sub"] = 'y';
// $nl_info["allow_any_sub"] = 'n';
// $nl_info["unsub_msg"] = 'y';
// $nl_info["validate_addr"] = 'y';

if (!isset($_REQUEST["edition_id"]))
	$_REQUEST["edition_id"] = 0;

if ($_REQUEST["edition_id"]) {
	$info = $nllib->get_edition($_REQUEST["edition_id"]);
} else {
	$info = array();

	$info["data"] = '';
	$info["subject"] = '';
}

$gBitSmarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	$nllib->remove_edition($_REQUEST["remove"]);
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

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'sent_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);

$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $nllib->list_editions($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('channels', $channels["data"]);

if( $gBitSystem->isFeatureActive( 'tiki_p_use_content_templates' ) ) {
	$templates = $tikilib->list_templates('newsletters', 0, -1, 'name_asc', '');
}

$gBitSmarty->assign_by_ref('templates', $templates["data"]);

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/send_newsletters.tpl');

?>

