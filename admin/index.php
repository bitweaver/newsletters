<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/Attic/index.php,v 1.1 2005/12/09 06:59:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../tiki_setup_inc.php' );

include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$gTikiSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["nl_id"])) {
	$_REQUEST["nl_id"] = 0;
}

$smarty->assign('nl_id', $_REQUEST["nl_id"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["nl_id"], 'newsletter')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'perm_name_desc', '', 'newsletters');

		foreach ($perms["data"] as $perm) {
			$perm_name = $perm["perm_name"];

			if ($userlib->object_has_permission($user, $_REQUEST["nl_id"], 'newsletter', $perm_name)) {
				$$perm_name = 'y';

				$smarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$smarty->assign("$perm_name", 'n');
			}
		}
	}
}

if ($tiki_p_admin_newsletters != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$gTikiSystem->display( 'error.tpl' );
	die;
}

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

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	check_ticket('admin-nl');
	$nllib->remove_newsletter($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-nl');
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
	$smarty->assign('nl_id', 0);
	$smarty->assign('info', $info);
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $nllib->list_newsletters($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

// Fill array with possible number of questions per page
/*
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$smarty->assign('freqs', $freqs);
*/
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/
ask_ticket('admin-nl');

// Display the template
$gTikiSystem->display( 'tikipackage:newsletters/admin_newsletters.tpl');

?>
