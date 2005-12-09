<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/Attic/index.php,v 1.3 2005/12/09 07:07:05 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'tiki_p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

if ($_REQUEST["nl_id"]) {
	$info = $nllib->get_newsletter($_REQUEST["nl_id"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["allow_user_sub"] = 'y';
	$info["allow_any_sub"] = 'n';
	$info["unsub_msg"] = 'y';
	$info["validate_addr"] = 'y';
}

$gBitSmarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	$nllib->remove_newsletter($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	if (isset($_REQUEST["allow_user_sub"]) && $_REQUEST["allow_user_sub"] == 'on') {
		$_REQUEST["allow_user_sub"] = 'y';
	} else {
		$_REQUEST["allow_user_sub"] = 'n';
	}

	if (isset($_REQUEST["allow_any_sub"]) && $_REQUEST["allow_any_sub"] == 'on') {
		$_REQUEST["allow_any_sub"] = 'y';
	} else {
		$_REQUEST["allow_any_sub"] = 'n';
	}

	if (isset($_REQUEST["unsub_msg"]) && $_REQUEST["unsub_msg"] == 'on') {
		$_REQUEST["unsub_msg"] = 'y';
	} else {
		$_REQUEST["unsub_msg"] = 'n';
	}

	if (isset($_REQUEST["validate_addr"]) && $_REQUEST["validate_addr"] == 'on') {
		$_REQUEST["validate_addr"] = 'y';
	} else {
		$_REQUEST["validate_addr"] = 'n';
	}

	$sid = $nllib->replace_newsletter($_REQUEST["nl_id"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["allow_user_sub"], $_REQUEST["allow_any_sub"], $_REQUEST["unsub_msg"], $_REQUEST["validate_addr"]);
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

$channels = $nllib->getList( $listHash );

$cant_pages = ceil( $channels["cant"] / $listHash['max_records'] );
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign( 'actual_page', 1 + ( $listHash['offset'] / $listHash['max_records'] ) );

if( $channels["cant"] > ( $listHash['offset'] + $listHash['max_records'] ) ) {
	$gBitSmarty->assign( 'next_offset', $offset + $listHash['max_records'] );
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if( $listHash['offset'] > 0) {
	$gBitSmarty->assign('prev_offset', $listHash['offset'] - $listHash['max_records']);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('channels', $channels["data"]);

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
