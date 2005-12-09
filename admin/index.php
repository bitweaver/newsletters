<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/Attic/index.php,v 1.4 2005/12/09 18:51:22 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'tiki_p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

if (isset($_REQUEST["remove"])) {
	$nllib->remove_newsletter($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	$sid = $gContent->store( $_REQUEST );
	/*
	$cat_type='newsletter';
	$cat_objid = $sid;
	$cat_desc = substr($_REQUEST["description"],0,200);
	$cat_name = $_REQUEST["name"];
	$cat_href= NEWSLETTERS_PKG_URL."newsletters.php?nl_id=".$cat_objid;
	include_once( CATEGORIES_PKG_PATH.'categorize_inc.php' );
	*/
	$info["name"] = '';
	$info["description"] = '';
	$info["allow_user_sub"] = 'y';
	$info["allow_any_sub"] = 'n';
	$info["unsub_msg"] = 'y';
	$info["validate_addr"] = 'y';
	//$info["frequency"] = 7 * 24 * 60 * 60;
	$gBitSmarty->assign('nl_id', 0);
	$gBitSmarty->assign('info', $info);
}

$newsletters = $gContent->getList( $listHash );
$gBitSmarty->assign_by_ref( 'newsletters', $newsletters );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash );

// Fill array with possible number of questions per page
/*
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$gBitSmarty->assign('freqs', $freqs);
*/
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/admin_newsletters.tpl');

?>
