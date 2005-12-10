<?php
/**
 *
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * created 2005/12/10
 *
 * @author spider <spider@steelsun.com>
 */

// Initialization
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( UTIL_PKG_PATH.'htmlMimeMail.php' );

$gBitSystem->verifyPackage( 'newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$listHash = array();
$newsletters = $gContent->mNewsletter->getList( $listHash );
$gBitSmarty->assign( 'newsletters', $newsletters );

if (isset($_REQUEST["preview"])) {
	$gBitSmarty->assign('preview', 'y');

	//$parsed = $tikilib->parse_data($_REQUEST["content"]);
	$parsed = $_REQUEST["edit"];
	$gBitSmarty->assign('parsed', $parsed);
	$info["data"] = $_REQUEST['edit'];
	$info["subject"] = $_REQUEST['title'];
	$gBitSmarty->assign('info', $info);
} elseif (isset($_REQUEST["save"])) {
	// Now send the newsletter to all the email addresses and save it in sent_newsletters
	$gBitSmarty->assign('presend', 'y');

	$subscribers = $nllib->get_subscribers($_REQUEST["nl_id"]);
	$gBitSmarty->assign('nl_id', $_REQUEST["nl_id"]);
	$gBitSmarty->assign('edit', $_REQUEST['edit']);
	$gBitSmarty->assign('subject', $_REQUEST['title']);
	$cant = count($subscribers);
	$gBitSmarty->assign('subscribers', $cant);
}

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/edit_edition.tpl' );


?>